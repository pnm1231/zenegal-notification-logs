<?php

/**
 * Our own string helper functions as well
 */
declare(strict_types = 1);

namespace Zenegal\NotificationLogs\Support;

/**
 * Class Str
 * @package Zenegal\NotificationLogs\Support
 */
class Str extends \Illuminate\Support\Str
{
    /**
     * The cache of studly-cased word strings.
     *
     * @var array
     */
    protected static $studlyWordCache = [];

    /**
     * Convert snake case to studly words
     *
     * ex: something_here to Something Here
     *
     * @param $value
     * @return mixed|string
     */
    public static function studlyWords($value)
    {
        $key = $value;

        if (isset(static::$studlyWordCache[$key])) {
            return static::$studlyWordCache[$key];
        }

        return static::$studlyWordCache[$key] = ucwords(str_replace(['-', '_'], ' ', $value));
    }
}
