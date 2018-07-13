<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PagamentoController extends AbstractActionController
{

    public function indexAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function confirmacaoAction()
    {
        return new ViewModel();
    }

    public function aguardarConfirmacaoAction()
    {
        return new ViewModel();
    }
}
