<?php

namespace Atelier;

class Names
{
    public static function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public static function snakeToCamel(string $input)
    {
        return str_replace('_', '', ucwords($input, '_'));
    }
}