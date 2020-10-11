<?php

namespace D15r\Butler\Support;

class Str
{
    public static function studly(string $value) : string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }
}