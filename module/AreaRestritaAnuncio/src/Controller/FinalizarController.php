<?php

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;

/**
 * Esse controller é só para as revendas
 */
class FinalizarController extends AbstractActionController
{
    public function onDispatch(MvcEvent $e)
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
