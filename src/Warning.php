<?php

namespace Atelier;

abstract class Warning
{
    /**
     * @var Machine[]
     */
    protected array $projects = [];

    public function __construct(protected array $type)
    {
    }

    public function getTypeId(): int
    {
        return $this->type['id'];
    }

    public function getTypeTitle(): string
    {
        return $this->type['title'];
    }

    public function getProblemsCount(): int
    {
        return count($this->projects);
    }

    /**
     * @return Machine[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    public function getProjectProblem(Project $project): string
    {
        return '';
    }

    public function getMachineProblem(Machine $machine): string
    {
        return '';
    }
}