<?php

namespace SnBH\Common\Logs;

use Laminas\Log\Logger;

class Login extends AbstractLog
{
    protected static function getChannel(): string
    {
        return 'LOGIN';
    }

    public static function success($idCadastro, $email)
    {
        static::doLog(
            Logger::INFO,
            'SUCCESS',
            [
                'idCadastro' => $idCadastro,
                'email' => $email,
            ]
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

    public static function captchaFail($email, $tipo)
    {
        static::doLog(
            Logger::WARN,
            'FAIL',
            ['email' => $email, 'tipo' => 'captcha:' . $tipo]
        );
    }
}
