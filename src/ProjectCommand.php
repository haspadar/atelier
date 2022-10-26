<?php

namespace Atelier;

use Atelier\Command\ExtractRotatorFragments;
use Atelier\Model\CommandTypes;
use Exception;

abstract class ProjectCommand extends Command
{
    /**
     * @var ProjectType[]
     */
    protected array $projectTypes;
    /**
     * @var Machine[]
     */
    protected array $projects;
    /**
     * @var Machine[]
     */
    protected array $machines;

    public function __construct(protected array $options = [])
    {
        parent::__construct($this->options);
        if (!$this->command) {
            throw new Command\Exception('Command not found in database');
        }

        $this->projectTypes = array_map(
            fn(array $type) => new ProjectType($type),
            (new CommandTypes())->getCommandTypes($this->command['id'])
        );
        $this->projects = Projects::getProjects(0, $this->projectTypes);
        $machineIds = array_unique(array_map(
            fn(Project $project) => $project->getMachine()->getId(),
            $this->projects
        ));
        $this->machines = Machines::getMachines($machineIds);
    }

    public function extractLastLine(string $response): string
    {
        $lines = array_values(array_filter(explode(PHP_EOL, $response)));

        return $lines[count($lines) - 1];
    }

    abstract public function run(Project $project): string;

    public function getDescription(): array
    {
        return array_merge(parent::getDescription(), [
            'Тип проектов' => implode(', ', array_map(fn(ProjectType $type) => $type->getName(), $command->getProjectTypes()))
                . ' (' . count($command->getMachines())
                . ' '
                . Plural::get(count($command->getMachines()), 'машина', 'машины', 'машин')
                . ')'
        ]);
    }

    /**
     * @param Machine[] $projects
     * @return Report|null
     */
    public function runForAll(array $projects = []): ?Report
    {
        try {
            $time = new ExecutionTime();
            $time->start();
            $typeNames = implode(
                ', ',
                array_map(fn(ProjectType $type) => $type->getName(), $this->getProjectTypes())
            );
            if (!$typeNames) {
                throw new Command\Exception('Не указаны типы проектов для команды');
            }

            Logger::info('Command ' . $this->getName() . ' started for every ' . $typeNames . ' project');
            $projects = $projects ?: $this->getProjects();
            $report = self::runForProjects($projects);
            $time->end();
            Logger::info('Command '
                . $this->getName()
                . ' processed '
                . count($projects)
                . ' projects and finished for '
                . $time->get()
            );
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }

        return $report ?? null;
    }

    /**
     * @return ProjectType[]
     */
    public function getProjectTypes(): array
    {
        return $this->projectTypes;
    }

    /**
     * @return Machine[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    public function getMachines(): array
    {
        return $this->machines;
    }

    protected function download(string $url): array
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL,$url);
        \curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
        \curl_setopt ($ch, CURLOPT_USERAGENT, "Home");

        $response = \curl_exec($ch);
        $info = curl_getinfo($ch);

        return [$response, $info];
    }

    protected function downloadHeaders(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Home");
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        return [$response, $info];
    }

    protected function replaceFileContent(Ssh $ssh, string $file, string $from, string $to): string
    {
        return $ssh->exec("sed -i 's/" . $this->escape($from) . '/' . $this->escape($to) . '/' . "' " . $file);
    }

    /**
     * @param Machine[] $projects
     */
    private function runForProjects(array $projects): ?Report
    {
        declare(ticks = 10) {
            $run = new Run();
            register_tick_function([$run, 'ping']);
            foreach ($projects as $project) {
                $ssh = $project->getMachine()->createSsh();
                if (!$ssh->getError()) {
                    $report = Reports::add($this, $project, null, $run);
                    Logger::debug('Run for '
                        . $project->getMachine()->getHost()
                        . ':'
                        . $project->getPath()
                        . '...'
                    );
                    $response = $this->run($project);
                    $report->finish($response);
                } else {
                    Logger::error($project->getMachine()->getHost() . ': ' . $ssh->getError());
                }
            }

            $run->finish();
        }

        return $report ?? null;
    }

    protected function replacePaltoSetting(Project $project, string $name, string $value): string
    {
        $dbCredentials = $this->extractDbCredentials($project);
        $error = $project->getMachine()->getSsh()->exec('mysql -u'
            . $dbCredentials->getUserName()
            . ' -p'
            . $dbCredentials->getPassword()
            . ' '
            . $dbCredentials->getDbName()
            . " -e \"UPDATE settings SET value='$value' WHERE name='$name';\"",
            '',
            $dbCredentials->getPassword());

        return $error;
    }

    protected function extractDbCredentials(Project $project): Project\Db
    {
        $response = $project->getMachine()->getSsh()->exec("cd " . $project->getPath() . ' && (cat config.php || cat configs/.env)');
        $lines = array_filter(explode(PHP_EOL, $response));
        $userName = $this->parseDbCredentialsLines($lines, ['DB::$user', 'DB_USER']);
        $password = $this->parseDbCredentialsLines($lines, ['DB::$password', 'DB_PASSWORD']);
        $dbName = $this->parseDbCredentialsLines($lines, ['DB::$dbName', 'DB_NAME']);

        return new Project\Db($userName, $password, $dbName);
    }

    protected function replaceFragments(Project $project, string $from, string $to)
    {
        $fragments = array_filter(
            RotatorFragments::getByProject($project),
            fn(RotatorFragment $fragment) => str_contains($fragment->getFragment(), $from)
        );
        Logger::debug('Found ' . count($fragments) . ' fragment(s)');
        foreach ($fragments as $fragment) {
            if ($error = $this->replaceFileContent(
                $project->getMachine()->getSsh(),
                $fragment->getPath(),
                $from,
                $to
            )) {
                Logger::error($error);
            } else {
                Logger::info('Replaced file ' . $fragment->getPath());
                (new ExtractRotatorFragments())->run($project);
                Logger::debug('ExtractRotatorFragments done');
            }
        }

        return implode(',', array_map(
            fn(RotatorFragment $rotatorFragment) => $rotatorFragment->getPath(),
            $fragments
        ));
    }

    private function escape(string $param)
    {
        return strtr($param, [
            ':' => '\:',
            '/' => '\/',
            '.' => '\.',
        ]);
    }

    private function parseDbCredentialsLines(array $lines, array $variables): string
    {
        foreach ($lines as $line) {
            foreach ($variables as $variable) {
                if (str_contains($line, $variable)) {
                    if ($value = trim(explode('=', $line)[1], "\n\r\t\v\x00'; ")) {
                        return $value;
                    }
                }
            }
        }

        return '';
    }
}