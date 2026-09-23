<?php

namespace SnBH\Common\Helper;

use SnBH\ApiClient\Client as ApiClient;

/**
 * Pede ao site público para descartar o cache da página do veículo.
 *
 * É melhor esforço: rodar depois que o dado já foi salvo e NUNCA pode travar nem
 * derrubar a requisição que salvou. A versão anterior fazia dois file_get_contents()
 * sem timeout para páginas pesadas do site; quando o site demorava, o POST em
 * /carro/dados ficava preso (até 3 min) e a pessoa via só o overlay branco de loading.
 */
class VeiculoClearCache
{
    /** Tempo máximo, em segundos, de cada chamada ao site. */
    private const TIMEOUT = 3;

    public static function clearCache(int|string $idVeiculo): void
    {
        try {
            $host = 'http://snbh-site';
            if (APPLICATION_ENV === 'production') {
                $host = 'https://seminovos.com.br';
            }

            self::get("{$host}/{$idVeiculo}?clear-cache=1");

            // phpcs:ignore
            global $sm;
            if (!$sm) {
                return;
            }

            /** @var ApiClient $apiClient */
            $apiClient = $sm->get(ApiClient::class);
            $data = $apiClient->veiculosGet(['ignorarCondicoesBasicas' => 1], $idVeiculo)->getData();
            $placa = $data[0]['placa'] ?? '';
            if ($placa === '') {
                return;
            }

            self::get("{$host}/veiculo-placa?consultaPlaca=true&placaId={$placa}&clear-cache=1");
        } catch (\Throwable $e) {
            error_log('[clear-cache] veiculo ' . $idVeiculo . ': ' . $e->getMessage());
        }
    }

    /**
     * GET com timeout curto. Descarta a resposta: só interessa o efeito no site.
     */
    private static function get(string $url): void
    {
        $ctx = stream_context_create([
            'http' => ['method' => 'GET', 'timeout' => self::TIMEOUT, 'ignore_errors' => true],
            'ssl'  => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        @file_get_contents($url, false, $ctx);
    }
}
