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
        $tipos = [
            'carro' => 1,
            'caminhao' => 2,
            'moto' => 3
        ];


        $viewModel = new ViewModel([
            'routeParams' => $this->params()->fromRoute(),
            'tipoCadastro' => $tipos[strtolower($this->params()->fromRoute('tipo'))],
            'idVeiculo' => null
        ]);

        $this->layout('layout/criar-anuncio');
        return $viewModel;
    }
}
