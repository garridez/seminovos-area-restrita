<?php

namespace SnBH\Common\Logs;

use Laminas\Log\Logger;

class Login extends AbstractLog
{

    protected static function getChannel(): string
    {
        return 'LOGIN';
    }

    public static function success($idCadastro)
    {
        static::doLog(
            Logger::INFO,
            'SUCCESS',
            ['idCadastro' => $idCadastro]
        );
    }

    public static function fail($email)
    {
        static::doLog(
            Logger::WARN,
            'FAIL',
            ['email' => $email]
        );
    }
}
