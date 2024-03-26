<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Pagamentos;
use AreaRestrita\Model\Veiculos;
use Laminas\Router\Http\RouteMatch;
use Laminas\View\Model\ViewModel;

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
        /** @var RouteMatch $routeMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function indexAction()
    {
        /** @var Pagamentos $historicoPagamentosModel */
        $historicoPagamentosModel = $this->getContainer()->get(Pagamentos::class);

        $dadosHistoricoPagamentos = $historicoPagamentosModel->get();

        /** @var Cadastros $cadastrosModel */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        $tipoCadastro = 1;

        if (!$cadastrosModel->isRevenda()) {
            /** @var Veiculos $veiculosModel */
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

        $arrayStatus = [
            1 => 'Aguardando Pagamento',
            2 => 'Aprovado',
            3 => 'Cancelado',
        ];

        $arrayFormaPagamento = [
            'cielo' => 'Cartão de Crédito',
            'deposito' => 'Depósito/Transferência',
            'pagseguro' => 'PagSeguro',
            'boleto' => 'Boleto',
            'creditcard' => 'Cartão de Crédito',
            'card' => 'Cartão de Crédito',
            'pix' => 'PIX',
        ];

        return new ViewModel([
            'historicoPagamentos' => $dadosHistoricoPagamentos['data'] ?? [],
            'arrayStatus' => $arrayStatus,
            'arrayFormaPagamento' => $arrayFormaPagamento,
            'tipoCadastro' => $tipoCadastro,
        ]);
    }
}
