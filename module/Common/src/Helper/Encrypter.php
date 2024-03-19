<?php

namespace SnBH\Common\Helper;

class Encrypter
{
    public static string $method = 'AES-256-CBC';
    public static string $key = '?:>}`$%&*()+6%6wV6+D_9m;}2Q';
    public static string $delimiter = '@#';

    public static function encrypt(string $plainText): string
    {
        $ivlen = openssl_cipher_iv_length(self::$method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($plainText, self::$method, self::$key, 0, $iv);
        return self::joinHash($encrypted, $iv);
    }

    public static function decrypt(string $hash): string|false
    {
        [$encryptedText, $iv] = self::splitHash($hash);
        return openssl_decrypt($encryptedText, self::$method, self::$key, 0, $iv);
    }

    protected static function splitHash(string $hash): array
    {
        [$encryptedText, $iv] = explode(self::$delimiter, $hash);
        $iv = hex2bin($iv);

        return [$encryptedText, $iv];
    }

    protected static function joinHash(string $encrypted, string $iv): string
    {
        $iv = bin2hex($iv);
        return $encrypted . self::$delimiter . $iv;
    }

    // phpcs:ignore
    public static function base64_encode(string $textToEncrypt): string
    {
        return base64_encode($textToEncrypt);
    }
}
