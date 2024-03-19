<?php

namespace SnBH\Common\Helper;

use SnBH\ApiClient\Client as ApiClient;

class VeiculoClearCache
{
    public static function clearCache(int|string $idVeiculo)
    {
        $host = 'http://snbh-site';
        if (APPLICATION_ENV === 'production') {
            $host = 'https://seminovos.com.br';
        }
        $url = "{$host}/{$idVeiculo}?clear-cache=1";
        @file_get_contents($url);

        // phpcs:ignore
        global $sm;

        /** @var ApiClient $apiClient */
        $apiClient = $sm->get(ApiClient::class);
        $data = $apiClient->veiculosGet(['ignorarCondicoesBasicas' => 1], $idVeiculo)->getData();
        if (!$data) {
            return;
        }

        @file_get_contents("{$host}/veiculo-placa?consultaPlaca=true&placaId={$data[0]['placa']}&clear-cache=1");
    }
}
