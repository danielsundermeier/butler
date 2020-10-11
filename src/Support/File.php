<?php

namespace D15r\Butler\Support;

class File
{
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
}