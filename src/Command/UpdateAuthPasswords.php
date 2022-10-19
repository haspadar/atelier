<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Project;
use Atelier\ProjectCommand;
use League\CLImate\CLImate;

class UpdateAuthPasswords extends ProjectCommand
{
    public function run(Project $project): string
    {
        try {
            $newAuthPassword = $this->options['auth_password'];
            $sudoPassword = $this->options['sudo_password'];
            $isDbChanged = $this->changeDatabaseSetting($project, $newAuthPassword);
            $isHtpasswdChanged = $this->changeHtpasswd($project, $newAuthPassword, $sudoPassword);

            return ($isDbChanged ? 'Пароль .htpasswd изменён' : 'Ошибка: пароль .htpasswd не изменён')
                . PHP_EOL
                . PHP_EOL
                . ($isHtpasswdChanged ? 'Пароль .htpasswd изменён' : 'Ошибка: пароль .htpasswd не изменён');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function parseEnv(string $content): array
    {
        $lines = explode(PHP_EOL, $content);
        $parsed = [];
        foreach ($lines as $line) {
            $parts = explode('=', $line);
            $name = trim($parts[0]);
            $value = trim($parts[1] ?? '');
            if ((str_starts_with($value, '"') && str_ends_with($value, '"'))
                || (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = mb_substr($value, 1, -1);
            }

            $parsed[$name] = $value;
        }

        return array_filter($parsed);
    }

    private function changeDatabaseSetting(Project $project, string $newAuthPassword): bool
    {
        $ssh = $project->getMachine()->getSsh();

        $env = $this->parseEnv($ssh->exec('cat ' . $project->getPath() . '/configs/.env'));
        $ssh->exec("mysql -e \"UPDATE " . $env['DB_NAME'] . ".settings SET password=\\\"$newAuthPassword\\\" name=\\\"auth_password\\\" \"");
        $response = $ssh->exec("mysql -u" . $env['DB_USER'] . " -p" . $env['DB_PASSWORD'] . " -e \"SELECT value FROM " . $env['DB_NAME'] . ".settings WHERE name=\\\"auth_password\\\" \"");
        $updatedAuthPassword = explode(PHP_EOL, $response)[1];

        return $updatedAuthPassword == $newAuthPassword;
    }

    private function changeHtpasswd(Project $project, string $newAuthPassword, string $sudoPassword): bool
    {
        $ssh = $project->getMachine()->getSsh();
        $response = $ssh->exec("htpasswd -b -c /tmp/.htpasswd palto $newAuthPassword");
        if (str_starts_with($response, 'Adding password')) {
            $ssh->exec("cp /tmp/.htpasswd " . $project->getPath(), $sudoPassword);

            return $ssh->exec('cat ' . $project->getPath() . '/.htpasswd') == $ssh->exec('cat /tmp/.htpasswd');
        }

        return false;
    }
}