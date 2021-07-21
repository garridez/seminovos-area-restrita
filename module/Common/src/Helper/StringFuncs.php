<?php

namespace SnBH\Common\Helper;

class StringFuncs
{

    public static function removerAcentos($string)
    {
        $string = trim($string);

        $string = preg_replace(
            array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/", "/(Ç)/"),
            explode(" ", "a A e E i I o O u U n N c C"),
            $string
        );

        return strtolower($string);
    }
    
    public static function removeCaractersEspecias($string)
    {

        return preg_replace('/([^a-zA-Z0-9])/', '', $string);
    }
    
}
