<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Planos;
use AreaRestrita\Model\Veiculos;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Response as ApiResponse;
use SnBH\ApiModel\Model\VeiculosInfo;
use Laminas\Mvc\Controller\AbstractActionController;

class PainelController extends AbstractActionController
{
	// TTLs
	protected int $cacheTtlVeiculosInfo = 86400; // 24h
	protected int $cacheTtlMetricas = 3600; // 1h
	protected int $gcThreshold = 420 * 1024 * 1024;

	public function indexAction(): ViewModel
	{
		$container = $this->getServiceContainer();

		/** @var Cadastros $cadastrosModel */
		$cadastrosModel = $container->get(Cadastros::class);
		$cadastro = $cadastrosModel->getCurrent();
		$idCadastro = $cadastro['idCadastro'] ?? 0;

		/** @var Planos $planosModel */
		$planosModel = $container->get(Planos::class);
		/** @var array $dadosPlanos */
		$dadosPlanos = $planosModel->get('revenda') ?? [];

		$key = array_search($cadastro['idPlano'] ?? null, array_column($dadosPlanos ?: [], 'idPlanoRevenda'));
		$valorPlanoRevenda = $dadosPlanos[$key]['valor'] ?? 0;

		/** @var Veiculos $veiculosModel */
		$veiculosModel = $container->get(Veiculos::class);
		$veiculos = $veiculosModel->getAll([
			'idCadastro' => $idCadastro,
		], 60 * 10);

		$veiculos['data'] = $veiculos['data'] ?? [];
		$totalVeiculos = $veiculos['total'] ?? count($veiculos['data']);
		$idsVeiculos = [];
		$totalVeiculosAtivos = 0;

		foreach ($veiculos['data'] as $veiculo) {
			if (in_array($veiculo['idStatus'] ?? null, [2, 8, 9], true)) {
				$totalVeiculosAtivos++;
			}
			$idsVeiculos[] = $veiculo['idVeiculo'] ?? 0;
		}

		/** @var ApiClient $apiClient */
		$apiClient = $container->get(ApiClient::class);

		$dateStart = $this->request->getQuery('date-start', 0);
		$dateEnd = $this->request->getQuery('date-end', 0);
		$filtradoPorData = (bool) ($dateStart || $dateEnd);

		// metricas por veiculo (agrupado por veiculo)
		$metricas = [];
		try {
			$metricas = $apiClient->veiculosMetricasGet([
				'idCadastro' => $idCadastro,
				'agruparPor' => 'veiculo',
				'incluirHistorico' => true,
				'dates' => [
					'start' => $dateStart,
					'end' => $dateEnd,
				],
				'dias' => 30,
			], null, $this->cacheTtlMetricas)->getData() ?? [];
		} catch (\Throwable $e) {
			error_log('veiculosMetricasGet error: ' . $e->getMessage());
			$metricas = [];
		}

		// metricas por data para gráficos
		$metricasPorData = [];
		try {
			$metricasPorData = $apiClient->veiculosMetricasGet([
				'idCadastro' => $idCadastro,
				'agruparPor' => 'data',
				'incluirHistorico' => true,
				'dates' => [
					'start' => $dateStart,
					'end' => $dateEnd,
				],
				'dias' => 30,
			], null, $this->cacheTtlMetricas)->getData() ?? [];
		} catch (\Throwable $e) {
			error_log('veiculosMetricasGet(data) error: ' . $e->getMessage());
			$metricasPorData = [];
		}

		/** @var array $maisAcessados */
		$maisAcessados = [];
		try {
			$maisAcessados = $apiClient->maisAcessadosGet([
				'qtd' => 30,
			], null, 60 * 60)->getData() ?? [];
		} catch (\Throwable $e) {
			error_log('maisAcessadosGet error: ' . $e->getMessage());
			$maisAcessados = [];
		}

		$apiClient->setStatusRangeCacheable(200, 404);

		/** @var VeiculosInfo $veiculosInfoModel */
		$veiculosInfoModel = $container->get(VeiculosInfo::class);

		// robust cache resolver com fallback embutido
		$localCache = $this->resolveLocalCache($container);

		// 1) Agrupar por modelo+anoFabricacao+anoModelo para evitar chamadas N por veiculo
		$groups = [];
		foreach ($veiculos['data'] as $idx => $v) {
			$modelo = $v['idModelo'] ?? 0;
			$anoF = $v['anoFabricacao'] ?? 0;
			$anoM = $v['anoModelo'] ?? 0;
			$groupKey = "{$modelo}:{$anoF}:{$anoM}";

			$groups[$groupKey]['indices'][] = $idx;
			if (!isset($groups[$groupKey]['repId'])) {
				$groups[$groupKey]['repId'] = $v['idVeiculo'] ?? 0;
				$groups[$groupKey]['modelo'] = $modelo;
				$groups[$groupKey]['anoFabricacao'] = $anoF;
				$groups[$groupKey]['anoModelo'] = $anoM;
			}
		}

		// 2) Resolver precoInfo por grupo (cache local)
		foreach ($groups as $groupKey => $meta) {
			$cacheKey = "veiculos-preco-{$groupKey}";
			$precoInfo = $this->cacheGet($localCache, $cacheKey);

			if ($precoInfo === null) {
				// tenta via model local primeiro
				try {
					$precoInfo = $veiculosInfoModel->get(
						$meta['modelo'],
						$meta['anoFabricacao'],
						$meta['anoModelo']
					) ?? [];
				} catch (\Throwable $e) {
					error_log('veiculosInfoModel->get error: ' . $e->getMessage());
					$precoInfo = [];
				}

				// se estiver vazio, usa um idVeiculo representativo para buscar FIPE via API
				$repId = (int) $meta['repId'];
				if ($repId) {
					try {
						/** @var ApiResponse $fipeData */
						$fipeData = $apiClient->veiculosInfoGet([], $repId, 60 * 60 * 24);
						$precoInfo['fipe'] = ($fipeData && $fipeData->status === 200) ? $fipeData->getData() : [];
					} catch (\Throwable $e) {
						error_log('veiculosInfoGet API error: ' . $e->getMessage());
						$precoInfo['fipe'] = [];
					}
				} else {
					$precoInfo['fipe'] = [];
				}

				$this->cacheSet($localCache, $cacheKey, $precoInfo, $this->cacheTtlVeiculosInfo);
			}

			// aplica precoInfo para todos os indices do grupo
			foreach ($meta['indices'] as $idx) {
				$veiculos['data'][$idx]['precoInfo'] = $precoInfo;
			}
		}

		// 3) Anexa métricas e aplica filtro por data (mantendo comportamento anterior)
		foreach ($veiculos['data'] as $k => $veiculo) {
			$idVeiculo = $veiculo['idVeiculo'] ?? 0;

			if ($filtradoPorData && empty($metricas[$idVeiculo])) {
				unset($veiculos['data'][$k]);
				continue;
			}

			if ($idVeiculo && isset($metricas[$idVeiculo])) {
				$veiculos['data'][$k]['metricas'] ??= [];
				foreach ($metricas[$idVeiculo] as $metricaName => $metricasRow) {
					$veiculos['data'][$k]['metricas'][$metricaName] = $metricasRow;
				}
			} else {
				$veiculos['data'][$k]['metricas'] = $veiculos['data'][$k]['metricas'] ?? [];
			}
		}

		return new ViewModel([
			'totalVeiculos' => $totalVeiculos,
			'totalVeiculosAtivos' => $totalVeiculosAtivos,
			'veiculos' => $veiculos,
			'valorPlanoRevenda' => $valorPlanoRevenda,
			'metricasPorData' => $metricasPorData,
			'maisAcessados' => $maisAcessados,
		]);
	}

	public function contadorPorMarcaAction(): JsonModel
	{
		$container = $this->getServiceContainer();
		$apiClient = $container->get(ApiClient::class);

		$contador = $apiClient->contadorGet(['marca' => true])->getData();

		return new JsonModel([
			'success' => 'SUCCESS',
			'data' => $contador,
		]);
	}

	public function contadorPorModeloAction(): JsonModel
	{
		$container = $this->getServiceContainer();
		$apiClient = $container->get(ApiClient::class);

		$contador = $apiClient->contadorGet(['modelo' => true])->getData();

		return new JsonModel([
			'success' => 'SUCCESS',
			'data' => $contador,
		]);
	}

	public function contadorPorCategoriaAction(): JsonModel
	{
		$container = $this->getServiceContainer();
		$apiClient = $container->get(ApiClient::class);

		$contador = $apiClient->contadorGet(['categoria' => true])->getData();

		return new JsonModel([
			'success' => 'SUCCESS',
			'data' => $contador,
		]);
	}

	public function detalheAnuncioAction(): ViewModel
	{
		$container = $this->getServiceContainer();
		$idVeiculo = (int) $this->params('idVeiculo');
		/** @var Veiculos $veiculoModel */
		$veiculoModel = $container->get(Veiculos::class);

		$veiculo = $veiculoModel->get($idVeiculo);

		$apiClient = $container->get(ApiClient::class);

		$dateStart = $this->request->getQuery('date-start', $veiculo['dataCadastro'] ?? 0);
		$dateEnd = $this->request->getQuery('date-end', 0);

		$metricas = [];
		try {
			$metricas = $apiClient->veiculosMetricasGet([
				'idVeiculo' => $idVeiculo,
				'dates' => [
					'start' => $dateStart,
					'end' => $dateEnd,
				],
				'dias' => 60,
			], null, 60 * 60)->getData()[$idVeiculo] ?? [
				'acesso' => [
					'total' => 0,
					'data' => [],
				],
				'impressao' => [
					'total' => 0,
					'data' => [],
				],
			];
		} catch (\Throwable $e) {
			error_log('veiculosMetricasGet detalhe error: ' . $e->getMessage());
			$metricas = [
				'acesso' => ['total' => 0, 'data' => []],
				'impressao' => ['total' => 0, 'data' => []],
			];
		}

		$cliques = $metricas['acesso']['total'] ?? 0;
		$impressoes = $metricas['impressao']['total'] ?? 0;
		$contato = 0;

		$frase = "";

		$temp_acoes = [
			"realizar_pagamento" => false,
			"editar_dados" => false,
			"editar_fotos" => false,
			"vendido" => false,
			"upgrade_plano" => false,
			"excluir" => false,
			"renovar" => false,
			"trocar_plano" => false,
			"reativar" => false,
			"enviar_comprovante" => false,
			"renovar_plano" => false,
			"inativar" => false,
		];

		switch ($veiculo['idStatus'] ?? '') {
			case "1":
				$frase = "";
				break;
			case "2":
				$frase = "Anúncio ativo no site";
				$temp_acoes["editar_dados"] = true;
				$temp_acoes["editar_fotos"] = true;
				$temp_acoes["excluir"] = true;
				$temp_acoes["inativar"] = true;
				$temp_acoes["trocar_plano"] = true;
				break;
			case "3":
				$frase = "";
				$temp_acoes["excluir"] = true;
				break;
			case "4":
				$frase = "";
				$temp_acoes["excluir"] = true;
				$temp_acoes["inativar"] = true;
				if (($veiculo['idPlano'] ?? 0) == 1) {
					$temp_acoes["trocar_plano"] = true;
				}
				break;
			case "5":
				$frase = "Anúncio inativo no site";
				$temp_acoes["editar_dados"] = true;
				$temp_acoes["editar_fotos"] = true;
				$temp_acoes["reativar"] = true;
				$temp_acoes["excluir"] = true;
				break;
			case "6":
			case "7":
			case "8":
			case "9":
				$frase = "";
				break;
			case "10":
				$frase = "";
				$temp_acoes["excluir"] = true;
				break;
			default:
				$temp_acoes = [
					"<h5>Huston we have a problem!!(Entre em contato com nosso suporte)</h5>",
				];
				break;
		}

		$veiculo['botoes'] = $temp_acoes;
		$veiculo['dataExpiracao'] = '';
		$veiculo['intervaloData'] = '';
		$veiculo['frase'] = $frase;

		return new ViewModel([
			'veiculo' => $veiculo,
			'cliques' => $cliques,
			'impressoes' => $impressoes,
			'contato' => $contato,
			'frase' => $frase,
			'metricas' => $metricas,
		]);
	}

	public function tabelaFipeAction(): JsonModel
	{
		$container = $this->getServiceContainer();
		$params = $this->params()->fromPost();

		$apiClient = $container->get(ApiClient::class);
		$data = $apiClient->versaoGet([
			'idModelo' => $params['modeloCarro'],
			'ano' => $params['ano'],
			'idMarca' => $params['idMarca'],
		])->getData();

		return new JsonModel([
			'success' => '200',
			'data' => $data,
		]);
	}

	/**
	 * Resolve o ServiceManager a partir do evento MVC.
	 */
	protected function getServiceContainer()
	{
		// prefer event -> application -> service manager
		if (method_exists($this, 'getEvent') && $this->getEvent()) {
			$app = $this->getEvent()->getApplication();
			if ($app && method_exists($app, 'getServiceManager')) {
				return $app->getServiceManager();
			}
		}

		// fallback para compatibilidade com versões antigas
		if (method_exists($this, 'getServiceLocator')) {
			return $this->getServiceLocator();
		}

		throw new \RuntimeException('ServiceManager not available in controller context');
	}

	/**
	 * Resolve um cache local de forma robusta ou retorna um cache em memória fallback.
	 *
	 * @param object $container
	 * @return mixed objeto com hasItem/getItem/setItem
	 */
	protected function resolveLocalCache($container)
	{
		$candidates = [
			'Cache',
			'cache',
			'laminas.cache',
			\Laminas\Cache\Storage\StorageInterface::class,
			'FilesystemCache',
			'RedisCache',
		];

		foreach ($candidates as $name) {
			try {
				if ($container->has($name)) {
					return $container->get($name);
				}
			} catch (\Throwable $e) {
				// ignora e tenta o próximo
			}
		}

		// fallback: cache simples em memória
		return new class {
			private array $store = [];
			public function hasItem(string $k): bool { return array_key_exists($k, $this->store); }
			public function getItem(string $k, $default = null) { return $this->store[$k] ?? $default; }
			public function setItem(string $k, $v): void { $this->store[$k] = $v; }
		};
	}

	/**
	 * Abstração para leitura do cache (compatível com fallback)
	 */
	protected function cacheGet($cache, string $key)
	{
		try {
			if (method_exists($cache, 'hasItem') && $cache->hasItem($key)) {
				return $cache->getItem($key);
			}
		} catch (\Throwable $e) {
			// ignore
		}
		return null;
	}

	/**
	 * Abstração para escrita no cache (compatível com fallback)
	 */
	protected function cacheSet($cache, string $key, $value, int $ttl = null): void
	{
		try {
			if (method_exists($cache, 'setItem')) {
				$cache->setItem($key, $value);
			}
		} catch (\Throwable $e) {
			// ignore
		}
	}
}