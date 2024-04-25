<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Model\Planos;
use Exception;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use SnBH\Common\Helper\MoveUpload;
use chillerlan\QRCode\QRCode;

class PagamentoController extends AbstractActionController
{
    public function indexAction()
    {
        $dadosVeiculo = $this->getVeiculo(5);
        $planos = $this->getContainer()
            ->get(Planos::class)
            ->getCurrent();

        $viewModel = new ViewModel([
            'planos' => $planos,
            'valorPlanoAtual' => (double) isset($dadosVeiculo['valorPlano']) ? $dadosVeiculo['valorPlano'] + 0.00 : 0.00,
            'idStatus' => $dadosVeiculo['idStatus'] ?? null,
            'idPlano' => $dadosVeiculo['idPlano'] ?? null,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    protected function getVeiculoData($idVeiculo = null)
    {
        if ($idVeiculo == null) {
            $idVeiculo = (int) $this->params()->fromQuery('idVeiculo');
        }
        if (!$idVeiculo) {
            return false;
        }

        $veiculo = $this->getApiClient()->veiculosGet([
            'ignorarCondicoesBasicas' => 1,
        ], $idVeiculo, 5);

        if (isset($veiculo->getData()[0])) {
            return $veiculo->getData()[0];
        }

        return false;
    }

    protected function getModelVeiculo($idVeiculo = null)
    {
        return new ViewModel([
            'veiculo' => $this->getVeiculoData($idVeiculo),
        ]);
    }

    public function aprovadoAction()
    {
        return $this->getModelVeiculo($this->params()->fromRoute('idVeiculo'));
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

    public function pagamentoPixAction()
    {
        return $this->getModelVeiculo();
    }

    public function pagamentoStatusAction()
    {
        if (!$this->getCadastro('idCadastro')) {
            $this->getResponse()->setStatusCode(401);
            return new JsonModel([
                'status' => 401,
            ]);
        }
        $dados = $this->params()->fromRoute();

        $dadosPag = $this->getApiClient()->pagamentosGet([
            'idCadastro' => $this->getCadastro('idCadastro'),
            'idVeiculo' => $dados['idVeiculo'],
            'sort' => 'idPagamento',
            'direction' => 'desc',
            'registrosPagina' => 1,
        ])->getData()[0] ?? [];

        return new JsonModel([
            'status' => 200,
            'ultimoPagamento' => array_intersect_key($dadosPag, [
                'status' => true,
                'forma_pagamento' => true,
                'data_cadastro' => true,
            ]),
        ]);
    }

    public function processarAction()
    {
        $dadosPagamento = [];
        $comprovanteAnexo = [];
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
        $idVeiculo = $dados['idVeiculo'] ?? null;
        $dadosPagamento['metodo'] = $dados['metodo'];
        $dadosPagamento['areaRestrita'] = 'nova';
        $dadosPagamento['total'] = $dados['total'] ?? null;
        $dadosPagamento['tempo_contrato'] = $dados['tempo_contrato'] ?? null;
        $dadosPagamento['idVeiculo'] = $idVeiculo;
        $dadosPagamento['idAnuncioVeiculo'] = $dados['idAnuncioVeiculo'] ?? null;
        $dadosPagamento['idCadastro'] = $cadastro['idCadastro'];
        $dadosPagamento['idPlano'] = $dados['idPlano'] ?? null;
        $dadosPagamento['flagCertificado'] = isset($dados['certificado']) && !empty($dados['certificado']) ? (int) $dados['certificado'] : null;
        $dadosPagamento['parcelas'] = 1;


        // dados para pagamento Cielo/cartão
        if ($dados['metodo'] == 'cielo' || $dados['metodo'] == 'card') {
            $dadosPagamento['numero_cartao'] = $dados['numero_cartao'] ?: $dados['number'];
            $dadosPagamento['nome_cartao'] = $dados['nome_cartao'] ?: $dados['name'];
            $dadosPagamento['validade_cartao'] = $dados['validade_cartao'] ?: $dados['expiry'];
            $dadosPagamento['cvc_cartao'] = $dados['cvc_cartao'] ?: $dados['cvc'];

            $dadosPagamento['parcelas'] = empty($dados['parcelas']) ? $dadosPagamento['parcelas'] : $dados['parcelas'];
            if ($dadosPagamento['parcelas'] > 8) {
                $dadosPagamento['parcelas'] = 8;
            }
            $dadosPagamento['tipo_pagamento'] = empty($dados['tipo_pagamento']) ? 'credito' : $dados['tipo_pagamento'];
        }
        $controle = false;
        $files = null;
        $apiClient = $this->getApiClient();

        $tempDir = $this->getContainer()->get('config')['dir']['upload'];
        $tempDir .= DIRECTORY_SEPARATOR . $idVeiculo;
        if (!file_exists($tempDir)) {
            mkdir($tempDir);
        }
        $moveUpload = new MoveUpload([
            'target' => $tempDir,
            'overwrite' => true,
            'randomize' => true,
            'use_upload_name' => true,
            'use_upload_extension' => true,
        ]);

        if (isset($_FILES) && $_FILES) {
            $comprovanteAnexo[] = $_FILES['comprovanteAnexo'];
            $files = $moveUpload->move($comprovanteAnexo, true);
            $controle = true;

            $arquivo = $files[0];
            $dadosPagamento[$apiClient::KEY_FILES] = [
                'comprovanteAnexo' => $arquivo,
            ];
        }

        $routeParams = $this->params()->fromRoute();
        $urlHelper = $this->url();
        $getUrlRedirect = function ($action) use ($urlHelper, $routeParams, $idVeiculo): string {
            $routeParams['action'] = $action;
            return $urlHelper->fromRoute('criar-anuncio/anuncio/pagamento/metodos', $routeParams) . '?idVeiculo=' . $idVeiculo;
        };
        /**
         * retorno quando é boleto da revenda feito pelo itau.
         **/
        /*if($cadastro['tipoCadastro'] == 1 && $dados['metodo'] == 'boleto'){
        $response['html'] = $this->getApiClient()
        ->pagamentosPost($dadosPagamento, null, false)
        ->getBody();
        echo json_encode($response);
        die;

        }*/

        /*echo $this->getApiClient()
        ->pagamentosPost($dadosPagamento, null, false)->getBody(); exit;*/

        $response = $this->getApiClient()
        ->pagamentosPost($dadosPagamento, null, false)
        ->json();

        if ($files) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        // Em caso de sucesso no pagamento
        if (isset($response['status']) && $response['status'] == 200) {
            if (($dados['metodo'] == 'cielo' || $dados['metodo'] == 'card') && isset($dados['placaVeiculo']) && $dados['certificado']) {
                $res = $apiClient->veiculosCertificadosPost(['placa' => $dados['placaVeiculo'], 'idVeiculo' => $idVeiculo, 'idCadastro' => $dadosPagamento['idCadastro']])->getData();
            }
            if ($dados['metodo'] == 'deposito' && $controle) {
                $response['data']['url'] = $getUrlRedirect('comprovante');
            }
            if ($dados['metodo'] == 'pix' && !isset($_FILES['comprovanteAnexo']) && $cadastro['tipoCadastro'] != 1) {
                $routeParams['action'] = 'pagamento-pix';
                $response['data']['url'] = $urlHelper->fromRoute('criar-anuncio/anuncio/pagamento/metodos', $routeParams) . '?idVeiculo=' . $idVeiculo . '&code=' . $response['data']['qr_code'] . '&idPagamento=' . md5($response['data']['idPagamento']);
            }
            if (!isset($response['data']['url']) && $cadastro['tipoCadastro'] != 1) {
                $response['data']['url'] = $getUrlRedirect('plano-renovado');
            }

            if ($dados['metodo'] == 'deposito' && !isset($_FILES['comprovanteAnexo'])) {
                $response['data']['url'] = $getUrlRedirect('aguardando-pagamento');
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
            $message = [];
            $message[] = '$response = ' . var_export($response, true);
            $message[] = '$dadosPagamento = ' . var_export($dadosPagamento, true);
            $message[] = '$_SESSION = ' . var_export($_SESSION, true);
            $message = '<pre>' . implode('<br><br><br>', $message) . '</pre>';

            mail('projetos@seminovosbh.com.br', $assunto, $message);
        }
        if (isset($response['type']) && $response && $response['type'] === 12001) {
            $response['detail'] = <<<HTML
                    <div class="text-center"><b>Aguarde!</b></div>
                    Ainda estamos processando uma outra tentativa de pagamento.<br>
                    O processo pode demorar de 5 a 10 minutos dependendo da sua operadora de cartão de crédito
HTML;
        }
        if ($dados['metodo'] == 'pix' && isset($response['data']['qr_code'])){
            $qrCode = new QRCode();
            $response['data']['img_qr_code'] = $qrCode->render($response['data']['qr_code']);
        }
        echo json_encode($response);
        die;
    }

    public function cancelarPagamentosEmAbertoAction(): never
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function retornoCieloAction(): never
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function retornoPagseguroAction(): never
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }
}
