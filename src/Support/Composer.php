<?php

namespace D15r\Butler\Support;

use Symfony\Component\Process\Process;

class Composer
{
    public static function addPathRepository(string $package_name, string $package_path) : bool
    {
        $params = json_encode([
            'type' => 'path',
            'url' => $package_path,
        ]);
        $command = [
            'composer',
            'config',
            'repositories.' . $package_name,
            $params,
            '--file',
            'composer.json',
        ];

        return self::run($command);
    }

    public static function removePathRepository(string $package_name)
    {
        return self::run([
            'composer',
            'config',
            '--unset',
            'repositories.' . $package_name,
        ]);
    }

    public static function makeJsonFile(string $package_path, array $replace, string $stub = 'plain.stub')
    {
        $stub = str_replace([
            '{{ namespace }}',
            '{{ package_name }}',
        ], $replace, file_get_contents(__DIR__ . '/../../stubs/packages/composer/' . $stub));
        file_put_contents($package_path . '/composer.json', $stub);
    }

    public static function addLaravelExtras() : void
    {

    }

    public static function install(string $package_name, string $package_path)
    {
        self::addPathRepository($package_name, $package_path);
        self::require($package_name, 'dev-master');
    }

    public static function uninstall(string $package_name)
    {
        self::removePathRepository($package_name);
        self::remove($package_name);
    }

    public static function require(string $package_name, string $version = '') : bool
    {
        $command = [
            'composer',
            'require',
            $package_name . ($version ? ':' . $version : '')
        ];

        return self::run($command);
    }

    public static function remove(string $package_name) : bool
    {
        return self::run([
            'composer',
            'remove',
            $package_name
        ]);
    }

    protected static function run(array $command) : bool
    {
        $process = new Process($command);
        $process->run();

        if ($process->getExitCode() > 0) {
            var_dump($process->getErrorOutput());
        }

        return ($process->getExitCode() === 0);
    }
}