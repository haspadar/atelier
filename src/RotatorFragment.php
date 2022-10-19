<?php

namespace Atelier;

class RotatorFragment
{
    public function __construct(
        private string $fragment,
        private string $path,
        private string $field,
        private \DateTime $createTime = new \DateTime()
    ) {
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return \DateTime
     */
    public function getCreateTime(): \DateTime
    {
        return $this->createTime;
    }
}