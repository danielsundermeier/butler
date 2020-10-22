<?php

namespace D15r\Butler\Support;

class Git
{
    public static function clone(string $package_path, string $repository_url) : void
    {
        exec('git clone ' . $repository_url . ' ' . $package_path);
    }

    public static function init(string $package_path)
    {
        exec('cd ' . $package_path . ' && git init && git add --all && git commit -m "Initial commit"');
    }

    public static function publish(string $package_path, string $url = '') : void
    {
        if (! file_exists($package_path . '/.git/')) {
            self::init($package_path);
        }

        if ($url) {
            exec('cd ' . $package_path . ' && git remote add origin ' . $url);
        }
        exec('cd ' . $package_path . ' && git push -u origin master');
    }

    public static function push(string $package_path, string $commit_message) : void
    {
        exec('cd ' . $package_path . ' && git add --all && git commit -m "' . $commit_message . '" && git push');
    }

    public static function release(string $package_path, string $version) : void
    {
        exec('cd ' . $package_path . ' && gh release create ' . $version);
    }

    public static function createRepository(string $package_path) : void
    {
        if (! file_exists($package_path . '/.git/')) {
            self::init($package_path);
        }

        exec('cd ' . $package_path . ' && gh repo create --public --confirm ' . str_replace('packages/', '', $package_path));
    }
}