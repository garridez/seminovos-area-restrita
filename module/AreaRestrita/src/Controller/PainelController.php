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

class PainelController extends AbstractActionController
{
	// TTLs
	protected int $cacheTtlVeiculosInfo = 86400; // 24h
	protected int $cacheTtlMetricas = 3600; // 1h

	public function indexAction(): ViewModel
	{
		/** @var Cadastros $cadastrosModel */
		$cadastrosModel = $this->getContainer()->get(Cadastros::class);
		$cadastro = $cadastrosModel->getCurrent();
		$idCadastro = $cadastro['idCadastro'];

		/** @var Planos $planosModel */
		$planosModel = $this->getContainer()->get(Planos::class);
		/** @var array $dadosPlanos */
		$dadosPlanos = $planosModel->get('revenda');

		$key = array_search($cadastro['idPlano'], array_column($dadosPlanos, 'idPlanoRevenda'));
		$valorPlanoRevenda = $dadosPlanos[$key]['valor'] ?? 0;

		/** @var Veiculos $veiculosModel */
		$veiculosModel = $this->getContainer()->get(Veiculos::class);
		$veiculos = $veiculosModel->getAll([
			'idCadastro' => $idCadastro,
		], 60 * 10);

		$totalVeiculos = $veiculos['total'] ?? 0;
		$idsVeiculos = [];
		$totalVeiculosAtivos = 0;

		foreach ($veiculos['data'] as $veiculo) {
			if (in_array($veiculo['idStatus'], [2, 8, 9])) {
				$totalVeiculosAtivos++;
			}
			$idsVeiculos[] = $veiculo['idVeiculo'];
		}

		/** @var ApiClient $apiClient */
		$apiClient = $this->getContainer()->get(ApiClient::class);

		$dateStart = $this->request->getQuery('date-start', 0);
		$dateEnd = $this->request->getQuery('date-end', 0);
		$filtradoPorData = (bool) ($dateStart || $dateEnd);

		// metricas por veiculo (agrupado por veiculo)
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

		// metricas por data para gráficos
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

		/** @var array $maisAcessados */
		$maisAcessados = $apiClient->maisAcessadosGet([
			'qtd' => 30,
		], null, 60 * 60)->getData() ?? [];

		$apiClient->setStatusRangeCacheable(200, 404);

		/** @var VeiculosInfo $veiculosInfoModel */
		$veiculosInfoModel = $this->getContainer()->get(VeiculosInfo::class);

		/** @var \Laminas\Cache\Storage\StorageInterface $localCache */
		$localCache = $this->getContainer()->get('Cache');

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
			$precoInfo = $localCache->hasItem($cacheKey) ? $localCache->getItem($cacheKey) : null;

			if ($precoInfo === null) {
				// tenta via model local primeiro
				$precoInfo = $veiculosInfoModel->get(
					$meta['modelo'],
					$meta['anoFabricacao'],
					$meta['anoModelo']
				) ?? [];

				// se estiver vazio, usa um idVeiculo representativo para buscar FIPE via API
				$repId = (int) $meta['repId'];
				if ($repId) {
					try {
						/** @var ApiResponse $fipeData */
						$fipeData = $apiClient->veiculosInfoGet([], $repId, 60 * 60 * 24);
						$precoInfo['fipe'] = ($fipeData && $fipeData->status === 200) ? $fipeData->getData() : [];
					} catch (\Throwable $e) {
						$precoInfo['fipe'] = [];
					}
				} else {
					$precoInfo['fipe'] = [];
				}

				$localCache->setItem($cacheKey, $precoInfo);
			}

			// aplica precoInfo para todos os indices do grupo
			foreach ($meta['indices'] as $idx) {
				$veiculos['data'][$idx]['precoInfo'] = $precoInfo;
			}
		}

		// 3) Anexa métricas e aplica filtro por data (mantendo comportamento anterior)
		foreach ($veiculos['data'] as $k => $veiculo) {
			$idVeiculo = $veiculo['idVeiculo'];

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
				// garante chave metricas existindo mesmo vazia (compatibilidade)
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
		$apiClient = $this->getContainer()->get(ApiClient::class);

		$contador = $apiClient->contadorGet(['marca' => true])->getData();

		return new JsonModel([
			'success' => 'SUCCESS',
			'data' => $contador,
		]);
	}

	public function contadorPorModeloAction(): JsonModel
	{
		$apiClient = $this->getContainer()->get(ApiClient::class);

		$contador = $apiClient->contadorGet(['modelo' => true])->getData();

		return new JsonModel([
			'success' => 'SUCCESS',
			'data' => $contador,
		]);
	}

	public function contadorPorCategoriaAction(): JsonModel
	{
		$apiClient = $this->getContainer()->get(ApiClient::class);

		$contador = $apiClient->contadorGet(['categoria' => true])->getData();

		return new JsonModel([
			'success' => 'SUCCESS',
			'data' => $contador,
		]);
	}

	public function detalheAnuncioAction(): ViewModel
	{
		$idVeiculo = (int) $this->params('idVeiculo');
		/** @var Veiculos $veiculoModel */
		$veiculoModel = $this->getContainer()->get(Veiculos::class);

		$veiculo = $veiculoModel->get($idVeiculo);

		$apiClient = $this->getContainer()->get(ApiClient::class);

		$dateStart = $this->request->getQuery('date-start', $veiculo['dataCadastro']);
		$dateEnd = $this->request->getQuery('date-end', 0);

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

		switch ($veiculo['idStatus']) {
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
				if ($veiculo['idPlano'] == 1) {
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
				$frase = "";
				break;
			case "7":
				$frase = "";
				break;
			case "8":
				$frase = "";
				break;
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
		$params = $this->params()->fromPost();

		$apiClient = $this->getContainer()->get(ApiClient::class);
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
}