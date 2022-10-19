<?php

namespace Atelier;

use Atelier\Model\PhpFpmTraffic;
use Atelier\Model\Projects;
use Atelier\Project\Type;
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

    public function getPhpFpmTraffic(): float
    {
        return $this->machine['php_fpm_traffic'];
    }

    public function getFreeSpace(): int
    {
        return $this->machine['free_space'];
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

    public function setFreeSpace(int $percent): void
    {
        (new \Atelier\Model\Machines())->update(['free_space' => $percent], $this->getId());
    }

    public function runPathCommand(string $command): array|string
    {
        return array_values(array_filter(
            $this->runCommand($command, true, fn($path) => $this->filterPath($path))
        ));
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
            if (!in_array($directory, ['', '/var/www/html'])
                && !$this->isArchive($directory)
                && !$this->isFile($directory)
            ) {
                $directories[] = $directory;
            }
        }

        return $directories;
    }

    /**
     * @return Project[]
     */
    public function getProjects(array $typeIds = []): array
    {
        return \Atelier\Projects::getProjects($this->getId(), $typeIds);
    }

    public function addProjects($directories): void
    {
        foreach ($directories as $directory) {
            (new Projects())->add([
                'machine_id' => $this->getId(),
                'type_id' => \Atelier\Projects::getType($this->ssh, $directory)->getId(),
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

    public function deleteProjects(int $machineId): void
    {
        (new Projects())->removeMachineProjects($machineId);
    }

    public function setPhpVersion(string $version): void
    {
        (new \Atelier\Model\Machines())->update(['php_version' => $version], $this->getId());
    }

    public function setPhpFpmActiveTime(?\DateTime $activeTime): void
    {
        (new \Atelier\Model\Machines())->update([
            'php_fpm_active_time' => $activeTime?->format('Y-m-d H:i:s')
        ], $this->getId());
    }

    public function addPhpFpmTraffic(string $traffic, \DateTime $activeTime): void
    {
        (new PhpFpmTraffic())->add([
            'machine_id' => $this->getId(),
            'traffic' => $traffic,
            'active_time' => $activeTime,
            'create_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
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

    private function isFile(string $file): bool
    {
        $ignoreFiles = ['.sql', '.env', '.htpasswd'];
        foreach ($ignoreFiles as $ignoreFile) {
            if (str_ends_with($file, $ignoreFile)) {
                return true;
            }
        }

        return false;
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