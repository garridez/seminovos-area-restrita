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
use AreaRestrita\Model\Veiculos;

class HistoricoPagamentosController extends AbstractActionController
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

        $dadosHistoricoPagamentos = $historicoPagamentosModel->get();

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        $tipoCadastro = 1;

        if (!$cadastrosModel->isRevenda()) {
            /* @var $veiculosModel Veiculos */
            $veiculosModel = $this->getContainer()->get(Veiculos::class);
            foreach ($dadosHistoricoPagamentos['data'] as $key => $row) {

                // Busca os dados do cadastro
                $dadosVeiculo = $veiculosModel->get($row['idVeiculo'], 120);

                $dadosHistoricoPagamentos['data'][$key]['nomePlano'] = $dadosVeiculo['nomePlano'];
                $dadosHistoricoPagamentos['data'][$key]['marca'] = $dadosVeiculo['marca'];
                $dadosHistoricoPagamentos['data'][$key]['modelo'] = $dadosVeiculo['modelo'];
                $dadosHistoricoPagamentos['data'][$key]['caracteristica'] = $dadosVeiculo['caracteristica'];
            }
            $tipoCadastro = 2;
        }

        $arrayStatus = array(
            1 => 'Aguardando Pagamento',
            2 => 'Aprovado',
            3 => 'Cancelado'
        );

        $arrayFormaPagamento = array(
            'cielo' => 'Cartão de Crédito',
            'deposito' => 'Depósito/Transferência',
            'pagseguro' => 'PagSeguro'
        );

        return new ViewModel([
            'historicoPagamentos' => $dadosHistoricoPagamentos['data'],
            'arrayStatus' => $arrayStatus,
            'arrayFormaPagamento' => $arrayFormaPagamento,
            'tipoCadastro' => $tipoCadastro
        ]);
    }
}
