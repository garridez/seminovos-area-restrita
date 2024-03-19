<?php

namespace SnBH\Common\Logs;

use Laminas\Log\Logger;

class Zoop extends AbstractLog
{
    protected static function getChannel(): string
    {
        return 'ZOOP';
    }

    public static function ok($data)
    {
        static::doLog(
            Logger::INFO,
            'ok',
            ['data' => $data]
        );
    }

    public static function ping()
    {
        static::doLog(
            Logger::INFO,
            'PING',
            []
        );
    }

    public static function fail($payload)
    {
        static::doLog(
            Logger::WARN,
            'FORMAT-INVALID',
            ['payload' => $payload]
        );
    }

    public static function notPost()
    {
        static::doLog(
            Logger::WARN,
            'NOT-POST',
        );
    }
}
