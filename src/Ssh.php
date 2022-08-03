<?php
namespace Atelier;

use phpseclib3\Crypt\Common\AsymmetricKey;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class Ssh
{
    private SSH2 $ssh2;

    private string $error = '';

    public function __construct(
        private readonly Machine $machine,
        private readonly string $login,
        private readonly string|AsymmetricKey $password
    ) {
        $this->validateParams();
        if (!$this->error) {
            $this->ssh2 = new SSH2($this->machine->getIp());
            $this->validateLogin();
        }
    }

    public function getError(): string
    {
        return $this->error;
    }

    private function validateParams()
    {
        if (!$this->machine) {
            $this->error = 'Укажите машину';
        } elseif (!$this->login) {
            $this->error = 'Укажите логин';
        } elseif (!$this->password) {
            $this->error = 'Укажите пароль';
        }
    }

    private function validateLogin(): void
    {
        if (!$this->ssh2->login($this->login, $this->password)) {
            $this->error = 'Неверные реквизиты';
        }
    }

    public function exec(string $command): string
    {
        Logger::debug('Running command "' . $command . '" on machine "' . $this->machine->getHost() . '"');
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $response = $this->ssh2->exec($command) ?? '';
        $executionTime->end();
        Logger::debug('Response: "' . $response . '", executed for ' . $executionTime->get());

        return $response;
    }
}