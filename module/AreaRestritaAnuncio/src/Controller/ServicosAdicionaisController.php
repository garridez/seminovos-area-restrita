<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ServicosAdicionaisController extends AbstractActionController
{
    public function indexAction()
    {
        $viewModel = new ViewModel();

        $viewModel->setTerminal(true);

        return $viewModel;
    }
}
