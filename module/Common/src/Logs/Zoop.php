<?php

namespace SnBH\Common\Logs;

use Laminas\Log\Logger;

class Zoop extends AbstractLog
{
    protected static function getChannel(): string
    {
        return 'ZOOP';
    }

    public static function ok(mixed $data): void
    {
        static::doLog(
            Logger::INFO,
            'ok',
            ['data' => $data]
        );
    }

    public static function ping(): void
    {
        static::doLog(
            Logger::INFO,
            'PING',
            []
        );
    }

    public static function fail(mixed $payload): void
    {
        static::doLog(
            Logger::WARN,
            'FORMAT-INVALID',
            ['payload' => $payload]
        );
    }

    public static function notPost(): void
    {
        static::doLog(
            Logger::WARN,
            'NOT-POST',
        );
    }
}
