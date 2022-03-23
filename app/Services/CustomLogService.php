<?php
namespace App\Services;
use Log;

class CustomLogService
{
    protected static function getFormat($file, $line, $class, $message)
    {
        $base = ["FILE", "LINE", "CLASS", "MESSAGE"];
        $convert   = [$file, $line, $class, $message];
        return str_replace($base, $convert, config('logging.log_format'));
    }

    public static function debug($file, $line, $class, $message)
    {
        if(config('logging.log_debug_enable'))
            Log::debug(self::getFormat($file, $line, $class,$message) );
    }

    public static function warning($file, $line, $class, $message)
    {
        Log::warning(self::getFormat($file, $line, $class,$message) );
    }

    public static function error($file, $line, $class, $message)
    {
        Log::error(self::getFormat($file, $line, $class,$message));
    }

    public static function info($file, $line, $class, $message)
    {
        Log::info(self::getFormat($file, $line, $class, $message) );
    }
}