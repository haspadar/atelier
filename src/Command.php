<?php

namespace Atelier;

use Atelier\Command\Exception;
use Atelier\Model\CommandTypes;
use Atelier\Model\ProjectTypes;
use DateTime;
use League\CLImate\CLImate;

abstract class Command
{
    protected array $command;

    public function __construct(protected array $options = [])
    {
        $classNameParts = explode('\\', get_class($this));
        $shortClassName = $classNameParts[count($classNameParts) - 1];
        $commandName = lcfirst($shortClassName);
        $this->generateCommand($commandName);
        $this->generateProjectTypes();
        $this->clearDbCommands();
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

    public function getRunTime(): ?DateTime
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

    private function generateCommand(string $commandName)
    {
        $this->command = (new \Atelier\Model\Commands())->getByName($commandName);
        if (Cli::isCli() && !$this->command) {
            $commandComment = (new CLImate())
                ->green()
                ->input("Команда $commandName не найдена в базе, введите описание – добавим:")
                ->prompt();
            (new \Atelier\Model\Commands())->add([
                'name' => $commandName,
                'comment' => $commandComment
            ]);
            $this->command = (new \Atelier\Model\Commands())->getByName($commandName);
        }
    }

    private function clearDbCommands(): void
    {
        $exists = array_map(
            fn(string $name) => substr(lcfirst(Names::snakeToCamel($name)), 0, -4),
            Directory::getBinScripts()
        );
        $removedCount = (new \Atelier\Model\Commands())->removeNotIn($exists);
        if ($removedCount && Cli::isCli()) {
            Logger::warning('Removed ' . $removedCount . ' rows from commands');
        }
    }

    private function generateProjectTypes(): void
    {
        $types = (new CommandTypes())->getCommandTypes($this->command['id']);
        if (!$types) {
            $allTypeNames = array_column((new ProjectTypes())->getAll(), 'name');
            $typeNames = (new CLImate())
                ->green()
                ->checkboxes("Выберите типы проектов для " . $this->command['name'] . ':', $allTypeNames)
                ->prompt();
            foreach ($typeNames as $typeName) {
                (new CommandTypes())->add([
                    'command_id' => $this->command['id'],
                    'project_type_id' => (new ProjectTypes())->getByName($typeName)['id']
                ]);
            }
        }
    }
}