<?php

namespace SnBH\Common\Helper;

class VeiculoClearCache
{

    static public function clearCache($idVeiculo)
    {
        $host = 'http://snbh-site';
        if (APPLICATION_ENV === 'production') {
            $host = 'https://seminovos.com.br';
        }
        $url = "{$host}/{$idVeiculo}?clear-cache=1";
        $url = "https://seminovos.com.br/{$idVeiculo}?clear-cache=1";
        @file_get_contents($url);
        
    }
}
