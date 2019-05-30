<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Model\Planos;
use Zend\View\Model\ViewModel;

class PagamentoController extends AbstractActionController
{

    public function indexAction()
    {
        $dadosVeiculo = $this->getVeiculo();

        $planos = $this->getContainer()
            ->get(Planos::class)
            ->getCurrent();

        $viewModel = new ViewModel([
            'planos' => $planos,
            'valorPlanoAtual' => (double) ($dadosVeiculo['valorPlano'] + 0.00),
            'idStatus' => $dadosVeiculo['idStatus'],
            'idPlano' => $dadosVeiculo['idPlano'],
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    protected function getVeiculoData($idVeiculo = null)
    {
        if ($idVeiculo == null) {
            $idVeiculo = (int) $this->params()->fromQuery('idVeiculo');
        }
        return $this->getApiClient()->veiculosGet([], $idVeiculo, true)->getData()[0];
    }

    protected function getModelVeiculo()
    {
        return new ViewModel([
            'veiculo' => $this->getVeiculoData()
        ]);
    }

    public function concluidoAction()
    {
        return $this->getModelVeiculo();
    }

    public function gratisAction()
    {
        return $this->getModelVeiculo();
    }

    public function comprovanteAction()
    {
        return $this->getModelVeiculo();
    }

    public function aguardandoPagamentoAction()
    {
        return $this->getModelVeiculo();
    }

    public function planoRenovadoAction()
    {
        return $this->getModelVeiculo();
    }

    public function processarAction()
    {
        $dados = $this->params()->fromPost();
        if (!$dados) {
            throw new Exception('Certifique-se de que os dados foram enviados.');
        }
        $cadastro = $this->getCadastro();

        // dados padrão
        if (isset($dados['acao'])) {
            $dadosPagamento['acao'] = $dados['acao'];
        }
        if (isset($dados['idPagamento'])) {
            $dadosPagamento['idPagamento'] = $dados['idPagamento'];
        }
        $idVeiculo = isset($dados['idVeiculo']) ? $dados['idVeiculo'] : null;
        $dadosPagamento['metodo'] = $dados['metodo'];
        $dadosPagamento['total'] = $dados['total'];
        $dadosPagamento['tempo_contrato'] = isset($dados['tempo_contrato']) ? $dados['tempo_contrato'] : null;
        $dadosPagamento['idVeiculo'] = $idVeiculo;
        $dadosPagamento['idAnuncioVeiculo'] = isset($dados['idAnuncioVeiculo']) ? $dados['idAnuncioVeiculo'] : null;
        $dadosPagamento['idCadastro'] = $cadastro['idCadastro'];
        $dadosPagamento['idPlano'] = $dados['idPlano'];
        $dadosPagamento['parcelas'] = 1;

        // dados para pagamento Cielo
        if ($dados['metodo'] == 'cielo') {
            $dadosPagamento['numero_cartao'] = $dados['numero_cartao'] ?: $dados['number'];
            $dadosPagamento['nome_cartao'] = $dados['nome_cartao'] ?: $dados['name'];
            $dadosPagamento['validade_cartao'] = $dados['validade_cartao'] ?: $dados['expiry'];
            $dadosPagamento['cvc_cartao'] = $dados['cvc_cartao'] ?: $dados['cvc'];

            $dadosPagamento['parcelas'] = !empty($dados['parcelas']) ? $dados['parcelas'] : $dadosPagamento['parcelas'];
            if ($dadosPagamento['parcelas'] > 8) {
                $dadosPagamento['parcelas'] = 8;
            }
            $dadosPagamento['tipo_pagamento'] = !empty($dados['tipo_pagamento']) ? $dados['tipo_pagamento'] : 'credito';
        }
        if (isset($_FILES) && $_FILES) {
            $dadosPagamento['files'] = array();
            foreach ($_FILES as $keyFile => $file) {

                $cFile = curl_file_create($file['tmp_name'], $file['type'], $file['name']);

                $dadosPagamento[$keyFile] = $cFile;
            }
        }

        $routeParams = $this->params()->fromRoute();
        $urlHelper = $this->url();
        $getUrlRedirect = function ($action) use($urlHelper, $routeParams, $idVeiculo) {
            $routeParams['action'] = $action;
            return $urlHelper->fromRoute('criar-anuncio/anuncio/pagamento/metodos', $routeParams) . '?idVeiculo=' . $idVeiculo;
        };



        $response = $this->getApiClient()
            ->pagamentosPost($dadosPagamento, null, false)
            ->json();

        // Em caso de sucesso no pagamento
        if (isset($response['status']) && $response['status'] == 200) {
            if (!isset($response['data']['url']) && $cadastro['tipoCadastro'] != 1) {
                $response['data']['url'] = $getUrlRedirect('planorenovado');
            }

            if ($dados['metodo'] == 'deposito' && $response['email_enviado']) {
                $response['data']['url'] = $getUrlRedirect('comprovante');
            }
            if ($dados['metodo'] == 'deposito' && !isset($_FILES['comprovanteAnexo'])) {
                $response['data']['url'] = $getUrlRedirect('aguardando-comprovante');
            }
            if (isset($response['data']['url'])) {
                $response['data']['redirect'] = true;
            }
        } else {
            // @todo Mudar isso aqui para um sistema de log
            $assunto = 'Erro pagamento';
            if (isset($response['detail'])) {
                $assunto .= ' - ' . $response['detail'];
            }
            $message = array();
            $message[] = '$response = ' . var_export($response, true);
            $message[] = '$dadosPagamento = ' . var_export($dadosPagamento, true);
            $message[] = '$_SESSION = ' . var_export($_SESSION, true);
            $message = '<pre>' . implode('<br><br><br>', $message) . '</pre>';

            mail('projetos@seminovosbh.com.br', $assunto, $message);
        }
        if (isset($response['type']) && $response) {
            switch ($response['type']) {
                case 12001:
                    $response['detail'] = <<<HTML
                    <div class="text-center"><b>Aguarde!</b></div>
                    Ainda estamos processando uma outra tentativa de pagamento.<br>
                    O processo pode demorar de 5 a 10 minutos dependendo da sua operadora de cartão de crédito
HTML;
                    break;
            }
        }
        echo json_encode($response);
        die;
    }

    public function cancelarPagamentosEmAbertoAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function retornoCieloAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function retornoPagseguroAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }
}
