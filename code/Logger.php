<?php
/**
 * Created by PhpStorm.
 * Date: 4/13/2017
 * Time: 17:01 PM
 */
namespace Voronenko\PHPOTP;

/**
 * Class Logger
 * @package Voronenko\PHPOTP
 *
 * @property string[] $trace
 */
class Logger
{
    public static $trace;

    /**
     * @param string $message
     * @return bool
     */
    public static function log($message)
    {
        return self::$trace[] = $message;
    }

    /**
     * @return string[]
     */
    public static function getTrace()
    {
        return self::$trace;
    }
}