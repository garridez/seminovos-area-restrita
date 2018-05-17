<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Planos;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Pagamentos;

class FinanceiroController extends AbstractActionController
{
    protected $container;
    protected $routeParams;
    protected $routeName;
    protected $idPlano;

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
        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        // Busca os dados do cadastro
        $dadosCadastro = $cadastrosModel->getCurrent(false);

        /* @var $planosModel Planos */
        $planosModel = $this->getContainer()->get(Planos::class);

        // Busca os planos de acordo com o tipo
        $dadosPlanos = $planosModel->get('revenda');

        $this->idPlano = $dadosCadastro['idPlano'];

        //filtra o array e retorna os dados de acordo com o idPlano
        $dadosPlano = array_filter($dadosPlanos, function ($dadosPlanos) {
            return $dadosPlanos['idPlanoRevenda'] == $this->idPlano;
        });

        /* @var $historicoPagamentosModel Pagamentos */
        $pagamentosModel = $this->getContainer()->get(Pagamentos::class);

        // Busca os dados do Pagamento/Fatura
        $dadosPagamento = $pagamentosModel->get();

        var_dump($dadosPagamento);exit;

        return new ViewModel();
    }
}