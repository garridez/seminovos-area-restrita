<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CriarAnuncioController extends AbstractActionController
{

    public function indexAction()
    {
        /**
         * Futuramente vamos exigir o login no meio do processo de criação
         */
        if (!$this->getCadastro()) {
            $this->redirect()->toUrl('/');
        }
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3
        ];
        $adicionalData = [];
        $params = $this->params();
        $idVeiculo = (int) $params->fromRoute('idVeiculo', false);

        if ($idVeiculo) {
            $data = $this->getApiClient()
                ->veiculosGet([
                    'ignorarCondicoesBasicas' => true,
                    ], (int) $idVeiculo, false)
                ->json();
            if ($data['status'] !== 200) {
                /**
                 * @todo Redirecionar para algum lugar e informar o erro
                 */
                die('O veículo não existe');
            }
            $data = $data['data'][0];
            $data['total'] = $data['valorPlano'];

            $data['idAnuncioVeiculo'] = $data['idAnuncio'] ?? null;

            $adicionalData = $data;
        }

        $viewModel = new ViewModel([
            'routeParams' => $params->fromRoute(),
            'tipoCadastro' => $tipos[strtolower($this->params()->fromRoute('tipo'))],
            ] + $adicionalData);

        $this->layout('layout/criar-anuncio');
        return $viewModel;
    }
}
