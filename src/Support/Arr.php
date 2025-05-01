<?php

declare(strict_types=1);

namespace Mlkali\Sa\Support;

class Arr
{
    public static string $path = '';

    public static function init(): void
    {
        self::$path = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR;
    }

    public static function pick(array $source, array $keys): array
    {
        return array_intersect_key($source, array_flip($keys));
    }

    public static function except(array $source, array $keys): array
    {
        return array_diff_key($source, array_flip($keys));
    }

    public static function flatten(array $array): array
    {
        $result = [];

        array_walk_recursive($array, function ($value) use (&$result) {
            $result[] = $value;
        });

        return $result;
    }
}
