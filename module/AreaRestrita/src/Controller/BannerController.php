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

class BannerController extends AbstractActionController
{

    protected $container;
    protected $routeParams;
    protected $routeName;

    public function __construct()
    {
        global $container;
        $this->container = $container;
    }

    /**
     * Listagem dos banners ja cadastrados
     */
    public function indexAction()
    {
        $apiClient = $this->getApiClient();


        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        $banners = $this->getApiClient()
                        ->SiteHospedadoBannerGet(['idSiteHospedado' => 102])
                        ->json();

        // var_dump($banners);
        // die;

        
        return new ViewModel([
            'banners' => $banners
        ]);
    }


    /**
     * View de cadastro de banners
     */
    public function cadastrarAction()
    {
        $apiClient = $this->getApiClient();


        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        $banners = $apiClient
                        ->SiteHospedadoBannerGet(['idSiteHospedado' => 102])
                        ->json();

        // var_dump($banners);
        // die;

        
        return new ViewModel([
            'banners' => $banners
        ]);
    }


    public function salvarAction()
    {
        $request = $this->getRequest();

        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        // Envia o banner
        $retorno = $this->getApiClient()->SiteHospedadoBannerPost($post);

        var_dump($retorno);
        die;
    }
}
