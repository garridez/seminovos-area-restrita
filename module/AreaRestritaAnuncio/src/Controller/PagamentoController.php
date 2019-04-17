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

        $planos = $this->getContainer()
            ->get(Planos::class)
            ->getCurrent();

        $viewModel = new ViewModel([
            'planos' => $planos
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function concluidoAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function comprovanteAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function aguardandoPagamentoAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function planoRenovadoAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function processarAction()
    {
        var_dump(__METHOD__ . ':' . __LINE__);
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
