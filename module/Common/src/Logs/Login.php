<?php

namespace SnBH\Common\Logs;

use Laminas\Log\Logger;

class Login extends AbstractLog
{
    protected static function getChannel(): string
    {
        return 'LOGIN';
    }

    /**
     * @param int $idCadastro
     * @param string $email
     */
    public static function success($idCadastro, $email): void
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

    /**
     * @param string $email
     */
    public static function fail($email): void
    {
        static::doLog(
            Logger::WARN,
            'FAIL',
            ['email' => $email]
        );
    }

    /**
     * @param string $email
     * @param string $tipo
     */
    public static function captchaFail($email, $tipo): void
    {
        static::doLog(
            Logger::WARN,
            'FAIL',
            ['email' => $email, 'tipo' => 'captcha:' . $tipo]
        );
    }
}
