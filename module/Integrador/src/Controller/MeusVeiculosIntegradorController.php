<?php

namespace SnBH\Integrador\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Pagamentos;
use AreaRestrita\Model\Propostas;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Service\Identity;
use DateTime;
use Laminas\Router\Http\RouteMatch;
use Laminas\View\Model\JsonModel;

class MeusVeiculosIntegradorController extends AbstractActionController
{
    protected $container;
    protected $routeParams;
    protected $routeName;

    public function __construct()
    {
        global $container;
        $this->container = $container;

        /**
         * Apenas para mostrar na view a rota
         */
        /** @var RouteMatch $routeMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function fetch()
    {
        $request = $this->request;
        $idCadastro = $this->params()->fromQuery('idCadastro');

        /** @var Veiculos $veiculosModel */
        $veiculosModel = $this->getContainer()->get(Veiculos::class);

        $request = $this->request;

        $page = $request->getQuery('page') ?? 1;

        // Busca os dados do cadastro
        $dadosVeiculos = $veiculosModel->getAll($page);

        /** @var Cadastros $cadastrosModel */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        $dataAtual = new DateTime(date('Y-m-d'));

        if ($cadastrosModel->isRevenda()) {
            /** @var Identity $identity */
            $identity = $this->getContainer()->get(Identity::class);
            $idCadastro = $identity->getIdentity();

            $dadosVeiculos = self::retornaValidacaoRevenda($dadosVeiculos);
        } else {
            $dadosVeiculos = self::retornaValidacaoParticular($dadosVeiculos);
        }

        $dadosVeiculos = self::retornaQuantidadePropostasVeiculo($dadosVeiculos);

        $routeName = str_replace("/meus-veiculos", "", (string) $request->getRequestUri());

        $routeParams = "/meus-veiculos";

        $paginationData = [
            'pages' => $dadosVeiculos['pages'],
            'total' => $dadosVeiculos['total'],
            'current' => $page ?? 1,
            'routeName' => $routeName,
            'routeParams' => $routeParams,
            'pagination' => true,
            'paginationResultado' => true,
        ];

        return new JsonModel([
            'paginationData' => $paginationData,
            'meusVeiculos' => $dadosVeiculos,
        ]);
    }

    protected function retornaValidacaoRevenda($dadosVeiculos)
    {
        /** Adicionado verificações para cada tipo de plano e status do anuncio */
        foreach ($dadosVeiculos['data'] as $key => $veiculo) {
            $dataAtual = new DateTime(date('Y-m-d'));

            $dataExpiracao = new DateTime($veiculo["dataExpiracao"]);
            $intevaloData = $dataAtual->diff($dataExpiracao);
            $intevaloData = (int) $intevaloData->format('%R%a');
            $dataExpiracao = $dataExpiracao->format('d/m/Y');

            $dataCadastro = new DateTime($veiculo["dataCadastro"]);
            $intervaloDataCadastro = $dataCadastro->diff($dataAtual);
            $intervaloDataCadastro = (int) $intervaloDataCadastro->format('%R%a');

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
                "certificado" => false,
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

            $dadosVeiculos['data'][$key]['botoes'] = $temp_acoes;
            $dadosVeiculos['data'][$key]['dataExpiracao'] = $dataExpiracao;
            $dadosVeiculos['data'][$key]['intervaloData'] = $intevaloData;
            $dadosVeiculos['data'][$key]['frase'] = $frase;
        }

        return $dadosVeiculos;
    }

    protected function retornaValidacaoParticular($dadosVeiculos)
    {
        if (!isset($dadosVeiculos['data'])) {
            return $dadosVeiculos;
        }
        /** Adicionado verificações para cada tipo de plano e status do anuncio */
        foreach ($dadosVeiculos['data'] as $key => $veiculo) {
            $dataAtual = new DateTime(date('Y-m-d'));

            $dataExpiracao = new DateTime($veiculo["dataExpiracao"]);
            $intevaloData = $dataAtual->diff($dataExpiracao);
            $intevaloData = (int) $intevaloData->format('%R%a');
            $dataExpiracao = $dataExpiracao->format('d/m/Y');

            $dataCadastro = new DateTime($veiculo["dataCadastro"]);
            $intervaloDataCadastro = $dataCadastro->diff($dataAtual);
            $intervaloDataCadastro = (int) $intervaloDataCadastro->format('%R%a');

            $dataTrocaStatus = new DateTime($veiculo["dataTrocaStatus"]);
            $dataAtual->format('Y-m-d H:i:s');
            $dataAtuall = new DateTime(date('Y-m-d H:i:s'));
            $intervaloDataTrocaStatus = $dataTrocaStatus->diff($dataAtuall);
            $intervaloDataTrocaStatus = (int) $intervaloDataTrocaStatus->format('%R%a');

            /** @var Pagamentos $pagamentosModel */
            $pagamentosModel = $this->getContainer()->get(Pagamentos::class);
            // Busca os dados do pagamento
            $pagamentosVeiculos = $pagamentosModel->get(null, 60);

            $statusPagamento = null;
            $statusPagamento = (int) $this->getVariavelltimoPagamentoVeiculo($pagamentosVeiculos, $veiculo['idVeiculo'], "status");

            $planoPagamento = null;
            $planoPagamento = (int) $this->getVariavelltimoPagamentoVeiculo($pagamentosVeiculos, $veiculo['idVeiculo'], "idPlano");

            $formaPagamento = null;
            $formaPagamento = $this->getVariavelltimoPagamentoVeiculo($pagamentosVeiculos, $veiculo['idVeiculo'], "formaPagamento");

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
                "alerta" => false,
                "certificado" => false,
            ];

            switch ($veiculo['idStatus']) {
                case "1":
                    $frase = "Aguardando confirmação de pagamento";
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["enviar_comprovante"] = $formaPagamento === "deposito";
                    $temp_acoes["trocar_plano"] = true;
                    $temp_acoes["plano_comprovante"] = $planoPagamento;
                    if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["editar_fotos"] = true;
                        $temp_acoes["realizar_pagamento"] = true;
                    }
                    break;
                case "2":
                    $frase = "Anúncio ativo no site";
                    $temp_acoes["vendido"] = true;
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["editar_fotos"] = true;
                    $temp_acoes["excluir"] = true;
                    if ($veiculo['flagCertificado'] != 1 && !empty($veiculo['placa'])) {
                        $temp_acoes["certificado"] = true;
                    }
                    if ($veiculo['idPlano'] != 4) {
                        $temp_acoes["upgrade_plano"] = true;
                    }
                    if ($veiculo['idPlano'] != 1 && $intevaloData <= 2 && $veiculo['veiculo_zero_km'] != 1) {
                        $temp_acoes["reativar"] = true;
                    }
                    if ($statusPagamento == 1) {
                        $temp_acoes["enviar_comprovante"] = true;
                        $temp_acoes["plano_comprovante"] = $planoPagamento;
                    }
                    break;
                case "3":
                    $frase = "Conclua o cadastro do anúncio";
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["trocar_plano"] = true;
                    $temp_acoes["editar_fotos"] = true;
                    $temp_acoes["excluir"] = true;
                   /* if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["editar_fotos"] = true;
                    }
                    if ($veiculo['idPlano'] != 4) {
                        $temp_acoes["upgrade_plano"] = true;
                    }*/
                    break;
                case "4":
                    $frase = "Renove seu anúncio (Os anúncios só podem ser editados após renovação)";
                    $temp_acoes["vendido"] = true;
                    $temp_acoes["excluir"] = true;
                    if ($veiculo['idPlano'] == 4 && $veiculo['veiculo_zero_km'] != 1) {
                        $temp_acoes["reativar"] = true;
                        //$temp_acoes["renovar"] = true;
                        $temp_acoes["renovar_plano"] = true;
                    } elseif ($veiculo['idPlano'] == 1) {
                        $temp_acoes["upgrade_plano"] = true;
                    } elseif ($veiculo["veiculo_zero_km"] == 1) {
                        $temp_acoes["trocar_plano"] = true;
                    } else {
                        $temp_acoes["reativar"] = true;
                    }
                    break;
                case "5":
                    $frase = "Anúncio inativo no site. Entre em conto com nosso atendimento";
                    $temp_acoes["alerta"] = true;
                    break;
                case "6":
                    $frase = "Aguardando liberação";
                    break;
                case "7":
                    $frase = "";
                    if ($veiculo['idPlano'] != 1 && $intervaloDataTrocaStatus <= 2) {
                        $temp_acoes["reativar"] = true;
                    }
                    break;
                case "8":
                    $frase = "Veículo vendido";
                    if ($veiculo['idPlano'] != 1 && ($intervaloDataTrocaStatus <= 2)) {
                        $temp_acoes["reativar"] = true;
                    }
                    break;
                case "9":
                    $frase = "";
                    if ($veiculo['idPlano'] != 4) {
                        $temp_acoes["upgrade_plano"] = true;
                    }
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["editar_fotos"] = true;
                    break;
                case "10":
                    $frase = "";
                    $temp_acoes["trocar_plano"] = true;
                    $temp_acoes["editar_dados"] = true;
                    $temp_acoes["excluir"] = true;
                    if ($veiculo['idPlano'] != 1) {
                        $temp_acoes["editar_fotos"] = true;
                    }
                    break;
                default:
                    $temp_acoes = [
                        "<h5>Huston we have a problem!!(Entre em contato com nosso suporte)</h5>",
                    ];
                    break;
            }

            $dadosVeiculos['data'][$key]['botoes'] = $temp_acoes;
            $dadosVeiculos['data'][$key]['dataExpiracao'] = $dataExpiracao;
            $dadosVeiculos['data'][$key]['intervaloData'] = $intevaloData;
            $dadosVeiculos['data'][$key]['frase'] = $frase;
        }

        return $dadosVeiculos;
    }

    /*
     * Verifica qual a ultima entrada de pagamento e captura a variavel solicitada desse
     * @param array $pagamentosVeiculos, int $idVeiculo, string $variavel
     * @return type $result
     */
    protected function getVariavelltimoPagamentoVeiculo($pagamentosVeiculos, $idVeiculo, $variavel)
    {
        if (!isset($pagamentosVeiculos['data'])) {
            return null;
        }

        $result = null;
        $auxData = null; //new \DateTime('1969-01-01');

        foreach ($pagamentosVeiculos['data'] as $pagamento) {
            if ($pagamento["idVeiculo"] == $idVeiculo) {
                $dataCadastro = new DateTime($pagamento["dataCadastro"]);

                if ($dataCadastro > $auxData) {
                    $auxData = $dataCadastro;
                    $result = $pagamento[$variavel];
                }
            }
        }

        return $result;
    }

    /*
     * Verifica qual a ultima entrada de pagamento e captura a variavel solicitada desse
     * @param array $pagamentosVeiculos, int $idCadastro, string $variavel
     * @return type $result
     */
    protected function getVariavelltimoPagamentoCadastro($pagamentosVeiculos, $idCadastro, $variavel)
    {
        if (!isset($pagamentosVeiculos['data'])) {
            return null;
        }

        $result = null;
        $auxData = null; //new \DateTime('1969-01-01');

        foreach ($pagamentosVeiculos['data'] as $pagamento) {
            if ($pagamento["idCadastro"] == $idCadastro) {
                $dataCadastro = new DateTime($pagamento["dataCadastro"]);

                if ($dataCadastro > $auxData) {
                    $auxData = $dataCadastro;
                    $result = $pagamento[$variavel];
                }
            }
        }

        return $result;
    }

    protected function retornaQuantidadePropostasVeiculo($dadosVeiculos)
    {
        /** @var Propostas $propostasModel */
        $propostasModel = $this->getContainer()->get(Propostas::class);
        if (!isset($dadosVeiculos['data'])) {
            return;
        }
        /**
         * Se for dev, não busca a propostas
         * A API de dev não conecta no banco de propostas
         * Então nem perde tempo procurando lá
         */
        if (IS_DEV) {
            return $dadosVeiculos;
        }

        $idVeiculos = array_column($dadosVeiculos['data'], 'idVeiculo');
        $dadosPropostas = $propostasModel->getAll($idVeiculos, 5 * 60) ?? [];

        $idVeiculoQtdPropostas = array_reduce($dadosPropostas, function ($acc, $row) {
            $acc[$row['idAnuncio']] = $acc[$row['idAnuncio']] ?? 0;
            $acc[$row['idAnuncio']]++;
            return $acc;
        }, []);

        foreach ($dadosVeiculos['data'] as $key => $veiculo) {
            $idVeiculo = $veiculo['idVeiculo'];

            $dadosVeiculos['data'][$key]['qdtPropostas'] = $idVeiculoQtdPropostas[$idVeiculo] ?? 0;
        }

        return $dadosVeiculos;
    }
}
