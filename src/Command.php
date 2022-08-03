<?php

namespace Atelier;

class Command
{
    public function __construct(private readonly array $command)
    {

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

    public function getRunTime(): ?\DateTime
    {
        return $this->command['run_time'];
    }
}