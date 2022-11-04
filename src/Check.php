<?php

namespace Atelier;

class Check
{
    public function __construct(private array $check)
    {

    }

    public function getId(): int
    {
        return $this->check['id'];
    }

    public function getProjectId(): ?int
    {
        return $this->check['project_id'];
    }

    public function getProjectName(): string
    {
        return Project::extractName($this->check['project_path']);
    }

    public function getMachineId(): int
    {
        return $this->check['machine_id'];
    }

    public function getMachineHost(): string
    {
        return $this->check['machine_host'];
    }

    public function getCreateTime(): \DateTime
    {
        return new \DateTime($this->check['create_time']);
    }

    public function getGroupTitle(): string
    {
        return $this->check['group_title'];
    }

    public function getText(): string
    {
        return $this->check['text'];
    }
}