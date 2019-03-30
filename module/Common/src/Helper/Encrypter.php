<?php

namespace SnBH\Common\Helper;

class Encrypter
{

    public static $method = 'AES-256-CBC';
    public static $key = '?:>}`$%&*()+6%6wV6+D_9m;}2Q';
    public static $delimiter = '@#';

    public static function encrypt($plainText)
    {
        $ivlen = openssl_cipher_iv_length(self::$method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($plainText, self::$method, self::$key, 0, $iv);
        return self::joinHash($encrypted, $iv);
    }

    public static function decrypt($hash)
    {
        list($encryptedText, $iv) = self::splitHash($hash);
        return openssl_decrypt($encryptedText, self::$method, self::$key, 0, $iv);
    }

    protected static function splitHash($hash)
    {
        list($encryptedText, $iv) = explode(self::$delimiter, $hash);
        $iv = hex2bin($iv);

        return [$encryptedText, $iv];
    }

    protected static function joinHash($encrypted, $iv)
    {
        $iv = bin2hex($iv);
        return $encrypted . self::$delimiter . $iv;
    }
}
