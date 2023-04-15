<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\SiteHospedado;
use AreaRestrita\Model\SiteHospedadoBanner;
use AreaRestrita\Model\SiteHospedadoConteudo;
use Laminas\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;

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
        /* @var $routeMatch \Laminas\Router\Http\RouteMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function indexAction()
    {
        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedadoModel = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedadoModel->get();

        /* @var $siteHospedadoConteudoModel SiteHospedadoConteudo */
        $siteHospedadoConteudoModel = $this->getContainer()->get(SiteHospedadoConteudo::class);

        $dadosSiteHospedadoConteudo = $siteHospedadoConteudoModel->get($dadosSiteHospedado[0]['idSiteHospedado']);

        /* @var $siteHospedadoBannerModel SiteHospedadoBanner */
        $siteHospedadoBannerModel = $this->getContainer()->get(SiteHospedadoBanner::class);

        $dadosSiteHospedadoBanner = $siteHospedadoBannerModel->get($dadosSiteHospedado[0]['idSiteHospedado']);

        return new ViewModel([
            'siteHospedado' => $dadosSiteHospedado[0],
            'siteHospedadoConteudo' => $dadosSiteHospedadoConteudo,
            'siteHospedadoBanner' => $dadosSiteHospedadoBanner,
        ]);
    }
}
