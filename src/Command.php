<?php

namespace Atelier;

use Atelier\Command\Exception;
use Atelier\Model\CommandTypes;
use League\CLImate\CLImate;

abstract class Command
{
    protected array $command;

    public function __construct(protected array $options = [])
    {
        $classNameParts = explode('\\', get_class($this));
        $shortClassName = $classNameParts[count($classNameParts) - 1];
        $this->command = (new \Atelier\Model\Commands())->getByName(lcfirst($shortClassName));
    }

    abstract public function runForAll(): ?Report;

    public function getDescription(): array
    {
        return [
            'Скрипт' => $this->getScript(),
            'Описание' => $this->getComment()
        ];
    }

    /**
     * @return array
     */
    public function getId(): int
    {
        return $this->command['id'];
    }

    public function getName(): string
    {
        return $this->command['name'];
    }

    public function getComment(): string
    {
        return $this->command['comment'];
    }

    public function getRunTime(): ?\DateTime
    {
        return $this->command['run_time'];
    }

    public function getLog(): string
    {
        return '';
    }

    public function getTooltip(): string
    {
        return '';
    }

    public function getScript(): string
    {
        return Names::camelToSnake($this->getName()) . '.php';
    }

    /**
     * @throws Exception
     */
    protected function requirePassword(string $password, string $welcome, string $error): string
    {
        if (!$password) {
            $password = (new CLImate())
                ->yellow()
                ->password($welcome)
                ->prompt();
        }

        if (!$password) {
            throw new Exception($error);
        }

        return $password;
    }

    protected function requireText(string $text, string $welcome, string $error): string
    {
        if (!$text) {
            $text = (new CLImate())
                ->yellow()
                ->input($welcome)
                ->prompt();
        }

        if (!$text) {
            throw new Exception($error);
        }

        return $text;
    }
}