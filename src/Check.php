<?php

namespace Atelier;

use Atelier\Model\CheckIgnores;

class Check
{
    public function __construct(private array $check)
    {

    }

    public function getId(): int
    {
        return $this->check['id'] ?? 0;
    }

    public function getProjectId(): ?int
    {
        return $this->check['project_id'] ?? null;
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

    public function ignoreMachine(): void
    {
        $machineId = $this->getMachineId();
        self::ignore();
        if (!($found = (new CheckIgnores())->find($this->getCommandName(), null, $machineId))) {
            (new CheckIgnores())->add([
                'command_name' => $this->getCommandName(),
                'project_id' => null,
                'machine_id' => $machineId,
                'create_time' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        } else {
            Logger::warning('Command already ignored: ' . var_export($found, true));
        }
    }

    public function ignoreProject(): void
    {
        self::ignore();
        if (!($found = (new CheckIgnores())->find($this->getCommandName(), $this->getProjectId(), null))) {
            (new CheckIgnores())->add([
                'command_name' => $this->getCommandName(),
                'project_id' => $this->getProjectId(),
                'machine_id' => null,
                'create_time' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
            self::ignore();
        } else {
            Logger::warning('Command already ignored: ' . var_export($found, true));
        }
    }

    public function getCommandId(): int
    {
        return $this->check['command_id'];
    }

    public function getCommandName(): string
    {
        return $this->check['name'];
    }

    public function ignore(): void
    {
        (new \Atelier\Model\Checks())->update([
            'ignore_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], $this->getId());
    }

    public function isIgnored(): bool
    {
        return !is_null($this->check['ignore_time']);
    }

    public function getTypeName(): string
    {
        return $this->check['type'];
    }

    public function getName(): string
    {
        return $this->check['name'];
    }
}