<?php

namespace SnBH\Common\Logs;

use Laminas\Log\Logger;

abstract class AbstractLog
{
    abstract protected static function getChannel(): string;

    protected static function doLog($priority, $message, $attributes = [])
    {
        $extra = [
            'channel' => static::getChannel(),
        ];

        $attributes['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];

        if ($attributes) {
            $extra['attributes'] = $attributes;
        }
        static::getLogger()->log($priority, $message, $extra);
    }

    protected static function getLogger(): Logger
    {
        global $logger;
        return $logger;
    }
}
