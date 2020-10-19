<?php

namespace D15r\Butler\Support;

class File
{
    public static function copySkeleton(string $source, string $package_path = '') : void
    {
        $destination = str_replace('/' . basename($package_path), '', $package_path);
        exec('cp -r ' . $source . ' ' . $destination);
        rename($destination . '/' . basename($source), $package_path);
    }

    public static function makeDirectory(string $path) : void
    {
        if (is_dir($path)) {
            return;
        }

        mkdir($path, 0777, true);
    }

    public static function deleteDirectory(string $path) : bool
    {
        if (! is_dir($path)) {
            return false;
        }

        if ($path == 'packages' || $path == '/') {
            return false;
        }

        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            $dir_path = $path . '/' . $file;
            if (is_dir($dir_path)) {
                self::deleteDirectory($dir_path);
            } else {
                @chmod($dir_path, 0777);
                @unlink($dir_path);
            }
        }

        return rmdir($path);
    }

    public static function delete(string $path)
    {
        unlink($path);
    }

    public static function replaceAllPlaceholders(string $path, array $search = [], array $replace = [])
    {
        $files = new \RecursiveDirectoryIterator($path);
        foreach (new \RecursiveIteratorIterator($files) as $file) {
            if (! $file->isFile()) {
                continue;
            }
            self::replaceContent($file->getPath().'/'.$file->getFilename(), $search, $replace);
        }
    }

    public static function renameFiles(string $path, array $search = [], array $replace = [])
    {
        $files = new \RecursiveDirectoryIterator($path);
        foreach (new \RecursiveIteratorIterator($files) as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $replaced = str_replace($search, $replace, $file->getFilename());
            if ($replaced === $file->getFilename()) {
                continue;
            }
            rename($file->getPath().'/'.$file->getFilename(), $file->getPath().'/'.$replaced);
        }
    }

    public static function replaceContent(string $path, array $search = [], array $replace = []) : int
    {
        return self::makeFromStub($path, $path, $search, $replace);
    }

    public static function makeFromStub(string $path, string $stub_path, array $search = [], array $replace = []) : int
    {
        $stub = file_get_contents($stub_path);

        if (count($search)) {
            $stub = str_replace($search, $replace, $stub);
        }

        return file_put_contents($path, $stub);
    }
}