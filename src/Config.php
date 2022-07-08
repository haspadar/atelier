<?php
namespace Atelier;

use Dotenv\Dotenv;

class Config
{
    /**
     * @var null[]|string[]
     */
    private static array $env;

    public static function get(string $name): string
    {
        return self::getEnv()[$name] ?? '';
    }

    public static function getEnv(): array
    {
        if (!isset(self::$env)) {
            self::$env = array_merge(
                Dotenv::createMutable(Directory::getConfigsDirectory(), '.env')->load(),
            );
        }

        return self::$env;
    }
}