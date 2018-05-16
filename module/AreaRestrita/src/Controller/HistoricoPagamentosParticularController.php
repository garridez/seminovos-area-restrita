<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Pagamentos;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;

class HistoricoPagamentosParticularController extends AbstractActionController
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
        /* @var $routeMatch \Zend\Router\Http\RouteMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function indexAction()
    {

        /* @var $historicoPagamentosModel Pagamentos */
        $historicoPagamentosModel = $this->getContainer()->get(Pagamentos::class);

        $dadosHistoricoPagamentos = $historicoPagamentosModel->get()->getData();

        $arrayDados = array();
        foreach ($dadosHistoricoPagamentos as $key => $dados) {
            $arrayDados[$key]['idPagamento'] = $dados['idPagamento'];
            $arrayDados[$key]['valor'] = $dados['valor'];
            $arrayDados[$key]['dataCadastro'] = $dados['dataCadastro'];
            $arrayDados[$key]['formaPagamento'] = $dados['formaPagamento'];
        }
        return new ViewModel([
            'historicoPagamentoParticular' => $arrayDados
        ]);
    }
}