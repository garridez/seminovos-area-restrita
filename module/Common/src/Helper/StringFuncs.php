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
		// Remove tudo que não for número
		$telefone = preg_replace('/[^0-9]/', '', (string) $telefone);

		// Verifica se é celular (11 dígitos) ou fixo (10 dígitos)
		if (preg_match('/^(\d{2})(\d{5})(\d{4})$/', $telefone, $m)) {
			// Celular: (31) 91234-5678
			return "($m[1]) $m[2]-$m[3]";
		} elseif (preg_match('/^(\d{2})(\d{4})(\d{4})$/', $telefone, $m)) {
			// Fixo: (31) 3234-5678
			return "($m[1]) $m[2]-$m[3]";
		}

		// Retorna original se não bater com nenhum dos dois formatos
		return $telefone;
    }
}
