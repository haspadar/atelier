<?php

namespace Atelier;

class Directory
{
    private static string $rootDirectory;

    public static function getRootDirectory(): string
    {
        if (!isset(self::$rootDirectory)) {
            $path = __DIR__;
            while (!file_exists($path . '/vendor') && $path != '/') {
                $path = dirname($path);
            }

            self::$rootDirectory = $path;
        }

        return self::$rootDirectory;
    }

    public static function getConfigsDirectory(): string
    {
        return self::getRootDirectory() . '/configs';
    }

    public static function getLogsDirectories(): array
    {
        $directories = self::getFilesWithDirectories(self::getLogsDirectory());

        return array_filter($directories, function (string $directory) {
            return is_dir(self::getLogsDirectory() . '/' . $directory);
        });
    }

    public static function getLogsDirectory(): string
    {
        return self::getRootDirectory() . '/logs';
    }

    public static function getTemplatesDirectory(): string
    {
        return self::getRootDirectory() . '/templates';
    }

    public static function getFilesWithoutDirectories(string $directory): array
    {
        return array_values(array_filter(
            self::getFilesWithDirectories($directory),
            fn ($file) => !is_dir($directory . '/' . $file)
        ));
    }

    public static function getFilesWithDirectories(string $directory): array
    {
        return array_values(array_filter(
            scandir($directory),
            fn($iterateDirectory) => !in_array($iterateDirectory, ['.', '..'])
        ));
    }

    public static function getDirectoryFilesRecursive(string $directory): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $files = array();
        foreach ($rii as $file) {
            if ($file->isDir()){
                continue;
            }

            $files[] = $file->getPathname();
        }

        return $files;
    }

    public static function getBinScripts(): array
    {
        return array_filter(
            scandir(self::getBinDirectory()),
            fn(string $file) => !in_array($file, ['.', '..', 'autoload_require_composer.php', 'list.php'])
        );
    }

    public static function getBinDirectory(): string
    {
        return self::getRootDirectory() . '/bin';
    }

    public static function getDbDirectory(): string
    {
        return self::getRootDirectory() . '/db';
    }

    public static function getPublicDirectory(): string
    {
        return self::getRootDirectory() . '/public';
    }
}