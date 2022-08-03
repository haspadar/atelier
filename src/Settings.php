<?php

namespace Atelier;

class Settings
{
    public static function getValues(): array
    {
        return (new \Atelier\Model\Settings())->getAll();
    }

    public static function getByName(string $name)
    {
        return (new \Atelier\Model\Settings())->getByName($name)['value'];
    }

    public static function getById(int $id)
    {
        return (new \Atelier\Model\Settings())->getById($id);
    }
}