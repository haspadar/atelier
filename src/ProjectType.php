<?php

namespace Atelier;

class ProjectType
{
    public function __construct(private array $type)
    {
    }

    public function getName(): string
    {
        return $this->type['name'];
    }
    
    public function getId(): int
    {
        return $this->type['id'];
    }
}