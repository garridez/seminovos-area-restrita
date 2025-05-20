<?php

namespace SnBH\Common\Helper;

class StringFuncs
{
    /**
     * @param string $string
     */
    public static function removerAcentos($string): string
    {
        $string = trim((string) $string);

        $string = preg_replace(
            ["/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/", "/(Ç)/"],
            explode(" ", "a A e E i I o O u U n N c C"),
            $string
        );

        return strtolower($string);
    }

    /**
     * @param string $string
     */
    public static function removeCaractersEspecias($string): string
    {
        return preg_replace('/([^a-zA-Z0-9])/', '', (string) $string);
    }

    public static function placaFormat(string $placa): string
    {
        $placa = strtoupper(trim($placa));
        $placa = substr($placa, 0, 3) . '-' . substr($placa, 3, 4);
        return $placa;
    }

    /**
     * Atalho para number_format
     */
    public static function nF(
        int|float $valor,
        int $decimals = 0,
        string $decimal_separator = ',',
        string $thousands_separator = '.'
    ): string {
        return number_format(
            $valor,
            $decimals,
            $decimal_separator,
            $thousands_separator
        );
    }

    public static function telefoneFormat(string| int $telefone): string
    {
        $telefone = preg_replace('/[^0-9]/', '',  (string) $telefone);
        $telefone = preg_replace('/^(.+)([0-9]{5})([0-9]{4})$/', ' ($1) $2-$3', $telefone);

        return $telefone;
    }
}
