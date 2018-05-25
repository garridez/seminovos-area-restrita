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
use AreaRestrita\Model\SiteHospedado;

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
            $dadosPlanos['idPlanoRevenda'] == $this->idPlano;
            return $dadosPlanos['idPlanoRevenda'] == $this->idPlano;
        });

        $valorPlano = array_values($dadosPlano)[0]['valor'];

        $valor = $dadosCadastro['icms'] == 'S' ? $valorPlano - ((4.3 / 100.0) * $valorPlano) : $valorPlano;

        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        $valorAdicional = sizeof($dadosSiteHospedado['data']) > 0 ? 29.00 : 0;
        $valorAdicionalString = 'R$ ' . number_format($valorAdicional, 2, ',', '.');
        $maisSite = sizeof($dadosSiteHospedado['data']) > 0 ? ' <i title="+ R$ ' . number_format($valorAdicional, 2, ',', '.') . '"> + Site</i>' : '';

        $dadosFinanceiro['valor'] = (float) $valorPlano;
        $dadosFinanceiro['valorAdicionalString'] = $valorAdicionalString;
        $dadosFinanceiro['maisSite'] = $maisSite;

        $dadosFinanceiro['valor_mensal'] = $valor;
        $dadosFinanceiro['adicional_mensal'] = $valorAdicional * 1;
        $dadosFinanceiro['total_mensal'] = 0;
        $dadosFinanceiro['economia_mensal'] = 0;
        $dadosFinanceiro['desconto_mensal'] = '0%';

        $dadosFinanceiro['valor_trimestral'] = $valorPlano * 3 - ((5.0 / 100.0) * $valor * 3);
        $dadosFinanceiro['adicional_trimestral'] = $valorAdicional * 3;
        $dadosFinanceiro['total_trimestral'] = 0;
        $dadosFinanceiro['economia_trimestral'] = (5.0 / 100.0) * $valorPlano * 3;
        $dadosFinanceiro['desconto_trimestral'] = '5%';

        $dadosFinanceiro['valor_semestral'] = $valorPlano * 6 - ((10.0 / 100.0) * $valor * 6);
        $dadosFinanceiro['adicional_semestral'] = $valorAdicional * 6;
        $dadosFinanceiro['total_semestral'] = 0;
        $dadosFinanceiro['economia_semestral'] = (10.0 / 100.0) * $valorPlano * 6;
        $dadosFinanceiro['desconto_semestral'] = '10%';

        $dadosFinanceiro['valor_anual'] = $valorPlano * 12 - ((15.0 / 100.0) * $valor * 12);
        $dadosFinanceiro['adicional_anual'] = $valorAdicional * 12;
        $dadosFinanceiro['total_anual'] = 0;
        $dadosFinanceiro['economia_anual'] = (15.0 / 100.0) * $valorPlano * 12;
        $dadosFinanceiro['desconto_anual'] = '15%';

        return new ViewModel([
            'financeiro' => $dadosFinanceiro
        ]);
    }
}