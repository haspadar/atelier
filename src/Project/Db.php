<?php

namespace Atelier\Project;

class Db
{
    public function __construct(
        private readonly string $userName,
        private readonly string $password,
        private readonly string $dbName
    ) {
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }
}