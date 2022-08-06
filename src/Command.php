<?php

namespace Atelier;

abstract class Command
{
    private array $command;

    public function __construct()
    {
        $classNameParts = explode('\\', get_class($this));
        $shortClassName = $classNameParts[count($classNameParts) - 1];
        $this->command = (new \Atelier\Model\Commands())->getByName(lcfirst($shortClassName));
    }

    abstract public function run(Project $project): string;

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
}