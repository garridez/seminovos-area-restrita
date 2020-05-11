<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Pagamentos;
use AreaRestrita\Model\Planos;
use AreaRestrita\Model\ServicosAdicionais;
use AreaRestrita\Model\SiteHospedado;
use SnBH\ApiClient\Client as ApiClient;
use Zend\View\Model\ViewModel;

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

        $idPlano = $this->idPlano = $dadosCadastro['idPlano'];

        //filtra o array e retorna os dados de acordo com o idPlano
        $dadosPlano = array_filter($dadosPlanos, function ($dadosPlanos) use($idPlano) {
            return $dadosPlanos['idPlanoRevenda'] == $idPlano;
        });

        /* @var $servicosAdicionaisModel ServicosAdicionais */
        $servicosAdicionaisModel = $this->getContainer()->get(ServicosAdicionais::class);

        // Busca os dados do ServicosAdicionais
        $dadosServicosAdicionais = $servicosAdicionaisModel->get(1); //o valor está fixo porque não foi encontrado no BD alternativa para consultar na tabela
        //valor adicional do serviço de site
        $valorServicoAdicional = $dadosServicosAdicionais['_embedded']['servicos_adicionais'][1][0]['valor'];

        $valorPlano = array_values($dadosPlano)[0]['valor'];

        $valor = $dadosCadastro['icms'] == 'S' ? $valorPlano - ((4.3 / 100.0) * $valorPlano) : $valorPlano;

        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        $valorAdicional = sizeof($dadosSiteHospedado) > 0 ? $valorServicoAdicional : 0;
        $valorAdicionalString = 'R$ ' . number_format($valorAdicional, 2, ',', '.');
        $maisSite = sizeof($dadosSiteHospedado) > 0 ? ' <i title="+ R$ ' . number_format($valorAdicional, 2, ',', '.') . '"> + Site</i>' : '';

        $valorPlanoAtual = (float) ($valorPlano + $valorAdicional);
        
        // Percentuais de descontos
        $descTrime = 5.0;
        $descSemes = 10.0;
        $descAnual = 15.0;
        
        $idCadastrosComDescondoCOVID = [
            207703, # Golaço Automóveis
            342744, # RC EMPREENDIMENTOS AUTOMOTIVOS
            271547, # Guariba Veículos
            26126,
        ];

        if (in_array($dadosCadastro['idCadastro'], $idCadastrosComDescondoCOVID)) {
            $descTrime = 10.0;
            $descSemes = 15.0;
            $descAnual = 20.0;
        }
        

        $dadosFinanceiro['valor'] = number_format($valorPlanoAtual, 2, ',', '.');
        $dadosFinanceiro['valorAdicionalString'] = $valorAdicionalString;
        $dadosFinanceiro['maisSite'] = $maisSite;

        $dadosFinanceiro['adicional_mensal'] = $valorAdicional * 1;
        $dadosFinanceiro['valor_mensal'] = number_format($valor + $dadosFinanceiro['adicional_mensal'], 2, ',', '.');
        $dadosFinanceiro['value_checkbox_mensal'] = $valor + $dadosFinanceiro['adicional_mensal'];
        $dadosFinanceiro['economia_mensal'] = number_format(0, 2, ',', '.');
        $dadosFinanceiro['desconto_mensal'] = '0%';

        $dadosFinanceiro['adicional_trimestral'] = $valorAdicional * 3;
        $dadosFinanceiro['valor_trimestral'] = number_format(($valorPlano * 3 - (($descTrime / 100.0) * $valor * 3)) + $dadosFinanceiro['adicional_trimestral'], 2, ',', '.');
        $dadosFinanceiro['value_checkbox_trimestral'] = ($valorPlano * 3 - (($descTrime / 100.0) * $valor * 3)) + $dadosFinanceiro['adicional_trimestral'];
        $dadosFinanceiro['economia_trimestral'] = number_format(($descTrime / 100.0) * $valorPlano * 3, 2, ',', '.');
        $dadosFinanceiro['desconto_trimestral'] = $descTrime . '%';

        $dadosFinanceiro['adicional_semestral'] = $valorAdicional * 6;
        $dadosFinanceiro['valor_semestral'] = number_format(($valorPlano * 6 - (($descSemes / 100.0) * $valor * 6)) + $dadosFinanceiro['adicional_semestral'], 2, ',', '.');
        $dadosFinanceiro['value_checkbox_semestral'] = ($valorPlano * 6 - (($descSemes / 100.0) * $valor * 6)) + $dadosFinanceiro['adicional_semestral'];
        $dadosFinanceiro['economia_semestral'] = number_format(($descSemes / 100.0) * $valorPlano * 6, 2, ',', '.');
        $dadosFinanceiro['desconto_semestral'] = $descSemes . '%';

        $dadosFinanceiro['adicional_anual'] = $valorAdicional * 12;
        $dadosFinanceiro['valor_anual'] = number_format(($valorPlano * 12 - (($descAnual / 100.0) * $valor * 12)) + $dadosFinanceiro['adicional_anual'], 2, ',', '.');
        $dadosFinanceiro['value_checkbox_anual'] = ($valorPlano * 12 - (($descAnual / 100.0) * $valor * 12)) + $dadosFinanceiro['adicional_anual'];
        $dadosFinanceiro['economia_anual'] = number_format(($descAnual / 100.0) * $valorPlano * 12, 2, ',', '.');
        $dadosFinanceiro['desconto_anual'] = $descAnual . '%';
        
//        var_dump($dadosFinanceiro);
//        die;

        return new ViewModel([
            'financeiro' => $dadosFinanceiro
        ]);
    }
}
