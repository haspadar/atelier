<?php

namespace Atelier;

use Atelier\Model\NginxTraffic;
use Atelier\Model\PhpFpmTraffic;
use Atelier\Model\Projects;
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

    public function getMysqlVersion(): string
    {
        return $this->machine['mysql_version'];
    }

    public function getPhpVersion(): string
    {
        return $this->machine['php_version'];
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
        return $this->machine['id'] ?? 0;
    }

    public function setFreeSpace(int $percent): void
    {
        (new \Atelier\Model\Machines())->update(['free_space' => $percent], $this->getId());
    }

    public function createSsh(string $login = '', string|AsymmetricKey $password = ''): Ssh
    {
        if (!$login && !$password) {
            $login = Settings::getByName('machine_default_login');
            $password = PublicKeyLoader::load(file_get_contents(Settings::getByName('ssh_key')));
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
            if (!in_array($directory, ['', '/var/www/html', '/var/www/status'])
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
            if (!\Atelier\Projects::isProjectIgnored($this->getId(), $directory)) {
                (new Projects())->add([
                    'machine_id' => $this->getId(),
                    'type_id' => \Atelier\Projects::getType($this->ssh, $directory)->getId(),
                    'path' => $directory,
                    'create_time' => (new \DateTime())->format('Y-m-d H:i:s')
                ]);
                Logger::info('Добавлен проект "' . $directory . '" для машины "' . $this->getHost() . '"');
            } else {
                Logger::warning('Проект "' . $directory . '" проигнорирован для машины "' . $this->getHost() . '"');
            }
        }
    }

    public function getNewDirectories(array $directories): array
    {
        $existsNames = array_map(fn(Project $project) => $project->getPath(), $this->getProjects());

        return array_values(array_diff($directories, $existsNames));
    }

    public function getNginxTraffic(): array
    {
        $projects = $this->getProjects();
        $traffic = [];
        foreach ($projects as $project) {
            $traffic[$project->getName()] = (new NginxTraffic())->getAll($project->getId());
        }

        return $traffic;
    }

    public function deleteProjects(int $machineId): void
    {
        (new Projects())->removeMachineProjects($machineId);
    }

    public function setMysqlVersion(string $version): void
    {
        (new \Atelier\Model\Machines())->update(['mysql_version' => $version], $this->getId());
    }

    public function setPhpVersion(string $version): void
    {
        (new \Atelier\Model\Machines())->update(['php_version' => $version], $this->getId());
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
        $archives = ['.zip', '.gz'];
        foreach ($archives as $archive) {
            if (str_ends_with($directory, $archive)) {
                return true;
            }
        }

        return false;
    }

    private function groupForChart(array $values): array
    {
        $keys = [];
        foreach ($values as $projectValues) {
            $keys = array_unique(array_merge($keys, array_keys($projectValues)));
        }

        $grouped = [];
        sort($keys);
        foreach ($values as $projectName => $projectValues) {
            $projectData = [];
            foreach ($keys as $key) {
                $projectData[$key] = intval($projectValues[$key]) ?? 0;
            }

            $grouped[] = [
                'name' => $projectName,
                'data' => $projectData
            ];
        }

        return $grouped;
    }
}