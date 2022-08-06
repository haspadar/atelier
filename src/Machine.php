<?php

namespace Atelier;

use Atelier\Model\Projects;
use Atelier\Project\ProjectType;
use phpseclib3\Crypt\Common\AsymmetricKey;
use phpseclib3\Crypt\PublicKeyLoader;

class Machine
{
    const PROJECTS_PATH = '/var/www/';
    private array $projectNames;
    private Ssh $ssh;

    public function __construct(private readonly array $machine)
    {
    }

    public function promptPassword(string $login, string $password): string
    {
        if (!$this->password || !$this->runCommand("echo {$this->password} | sudo -S cat /etc/crontab")) {
            return $this->promptPassword();
        }

        return $this->password;
    }

    public function getHost(): string
    {
        return $this->machine['host'];
    }

    public function getIp(): string
    {
        return $this->machine['ip'];
    }

    public function getId(): int
    {
        return $this->machine['id'];
    }

    public function runPathCommand(string $command): array|string
    {
        return array_values(array_filter(
            $this->runCommand($command, true, fn($path) => $this->filterPath($path))
        ));
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getFileContent(string $filename): string
    {
        return $this->runCommand("cat " . $filename, false);
    }

    public function forEveryProject(callable $projectLogic, bool $isPaltoOnly = false)
    {
        $projects = array_map(
            fn(string $name) => new Project($this, $name),
            $this->runPathCommand("ls -d " . self::PROJECTS_PATH . "*/")
        );
        if ($isPaltoOnly) {
            $projects = array_filter($projects, fn(Project $project) => $project->isPalto());
        }

        foreach ($projects as $projectKey => $project) {
            $machineLogMessage = 'Directory ' . $project->getDirectory() . ' (' . ($projectKey + 1) . '/' . count($projects) . ')';
            Logger::machineInfo($machineLogMessage);
            $projectLogic($project);
        }
    }

    public function isFileExists(string $file): bool
    {
        $response = $this->runCommand("test -e " . $file . '  && echo file exists || echo file not found', false);
        $isExists = $response == 'file exists';
        Logger::debug('is file ' . $file . ' exists on machine ' . $this->getHost() . ': ' . ($isExists ? 'true' : 'false'));

        return $isExists;
    }

    public function runSudoCommand(string $command, bool $isArray = true, ?callable $filter = null): array|string
    {
        $sudoPassword = $this->password;

        return $this->runCommand("echo $sudoPassword | sudo -S " . $command, $isArray, $filter);
    }

    /**
     * @param string $command
     * @param bool $isArray
     * @param callable|null $filter
     * @return array|string
     */
    public function runCommand(string $command, bool $isArray = true, ?callable $filter = null): array|string
    {
        $fullCommand = "ssh -T $this->name \"" . $command . "\"";
        $filteredCommand = $this->password
            ? str_replace($this->password, '[PASSWORD_HIDDEN]', $fullCommand)
            : $fullCommand;
        Logger::machineDebug('Running command: ' . $filteredCommand);
        $response = `$fullCommand`;
        if ($response) {
            Logger::machineDebug($response);
        }

        $responseLines = array_values(array_filter(explode(PHP_EOL, $response ?? ''), $filter ?? null));

        return $isArray ? $responseLines : implode(PHP_EOL, $responseLines);
    }

    private function filterPath(string $path): bool
    {
        $firstSymbol = mb_substr($path, 0, 1);

        return $firstSymbol == '/';
    }

    public function findPaltoProjects()
    {
//        $directories = $this->getWithFileDirectories(self::PROJECTS_PATH, 'phinx.php', 2);
//        $filteredDirectories = $this->filterProjectNames($directories);
//
//        return array_map(fn($name) => new Project($this, $name), $filteredDirectories);
    }

    public function createSsh(string $login = '', string|AsymmetricKey $password = ''): Ssh
    {
        if (!$login && !$password) {
            $login = 'km';
            $password = PublicKeyLoader::load(file_get_contents('/Users/haspadar/.ssh/id_rsa_km'));
        }

        $this->ssh = new Ssh($this, $login, $password);

        return $this->ssh;
    }

    public function getSsh(): Ssh
    {
        return $this->ssh;
    }

    public function scanProjectDirectories(): array
    {
        $response = $this->ssh->exec("ls -d /var/www/*") ?? '';
        $directories = [];
        foreach (explode(PHP_EOL, $response) as $directory) {
            if (!in_array($directory, ['', '/var/www/html']) && !$this->isArchive($directory)) {
                $directories[] = $directory;
            }
        }

        return $directories;
    }

    /**
     * @return Project[]
     */
    public function getProjects(?int $typeId = null): array
    {
        return \Atelier\Projects::getProjects($this->getId(), $typeId);
    }

    /**
     * @return Project[]
     */
    public function getPaltoProjects(): array
    {
        $directories = $this->getWithFileDirectories(self::PROJECTS_PATH, 'phinx.php', 2);
        $filteredDirectories = $this->filterProjectNames($directories);

        return array_map(fn($name) => new Project($this, $name), $filteredDirectories);
    }

    public function addProjects($directories): void
    {
        foreach ($directories as $directory) {
            (new Projects())->add([
                'machine_id' => $this->getId(),
                'type' => \Atelier\Projects::getType($this->ssh, $directory)->name,
                'path' => $directory,
                'create_time' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
            Logger::info('Добавлен проект "' . $directory . '" для машины "' . $this->getHost() . '"');
        }
    }

    public function getNewDirectories(array $directories): array
    {
        $existsNames = array_map(fn(Project $project) => $project->getPath(), $this->getProjects());

        return array_diff($directories, $existsNames);
    }

    public function deleteProjects(int $machineId)
    {
        (new Projects())->removeMachineProjects($machineId);
    }

    private function getWithFileDirectories(string $directory, string $file, int $maxDepth = 4)
    {
        return array_map(
            function (string $name) use ($directory, $file) {
                preg_match(
                    '/('
                    . str_replace('/', '\/', $directory)
                    . "(.+)"
                    . '\/)'
                    . str_replace('.', '\.', $file)
                    . '/',
                    $name,
                    $matches
                );

                return $matches[1];
            },
            $this->runPathCommand("find " . $directory . " -maxdepth $maxDepth -name " . $file)
        );
    }

    private function filterProjectNames(array $directories): array
    {
        if (!$this->projectNames) {
            return $directories;
        }

        $filtered = [];
        foreach ($directories as $directory) {
            $directoryParts = array_values(array_filter(explode('/', $directory)));
            $projectName = $directoryParts[count($directoryParts) - 1];
            if (in_array($projectName, $this->projectNames)) {
                $filtered[] = $directory;
            }
        }

        return $filtered;
    }

    private function isArchive(string $directory): bool
    {
        $archives = ['.zip', '.tar.gz'];
        foreach ($archives as $archive) {
            if (str_ends_with($directory, $archive)) {
                return true;
            }
        }

        return false;
    }
}