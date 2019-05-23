<?php

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Esse controller é só para as revendas
 */
class FinalizarController extends AbstractActionController
{

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if ($this->getCadastro('tipoCadastro') != 1) {
            die;
        }
        parent::onDispatch($e);
    }

    public function indexAction()
    {
        $viewModel = new ViewModel([]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}
