<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\SiteHospedado;
use AreaRestrita\Model\SiteHospedadoBanner;
use AreaRestrita\Model\SiteHospedadoConteudo;
use Laminas\Router\Http\RouteMatch;
use Laminas\View\Model\ViewModel;

class MeuSiteController extends AbstractActionController
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
        /** @var SiteHospedado $siteHospedadoModel */
        $siteHospedadoModel = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedadoModel->get();

        /** @var SiteHospedadoConteudo $siteHospedadoConteudoModel */
        $siteHospedadoConteudoModel = $this->getContainer()->get(SiteHospedadoConteudo::class);

        $dadosSiteHospedadoConteudo = $siteHospedadoConteudoModel->get($dadosSiteHospedado[0]['idSiteHospedado']);

        /** @var SiteHospedadoBanner $siteHospedadoBannerModel */
        $siteHospedadoBannerModel = $this->getContainer()->get(SiteHospedadoBanner::class);

        $dadosSiteHospedadoBanner = $siteHospedadoBannerModel->get($dadosSiteHospedado[0]['idSiteHospedado']);

        return new ViewModel([
            'siteHospedado' => $dadosSiteHospedado[0],
            'siteHospedadoConteudo' => $dadosSiteHospedadoConteudo,
            'siteHospedadoBanner' => $dadosSiteHospedadoBanner,
        ]);
    }
}
