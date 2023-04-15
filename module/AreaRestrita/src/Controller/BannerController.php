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
use Laminas\View\Model\ViewModel;

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
        $banners = [];
        $apiClient = $this->getApiClient();

        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();
        
        $dadosSite = $dadosSiteHospedado[0]['idSiteHospedado']?? null;
        
        if($dadosSite){
            $banners = $this->getApiClient()
                        ->SiteHospedadoBannerGet(['idSiteHospedado' => $dadosSite])
                        ->json();
        }

        return new ViewModel([
            'banners' => $banners['dados']??[],
            'siteHospedado' => $dadosSiteHospedado
        ]);
    }


    /**
     * View de cadastro de banners
     */
    public function cadastrarAction()
    {
        return new ViewModel();
    }


    public function salvarAction()
    {
        $request = $this->getRequest();
        $apiClient = $this->getApiClient();

        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        if (!isset($dadosSiteHospedado[0])) {
            return $this->redirect()->toUrl('/banners');
        }

        $arrayFotos = array_map(function($img) {
            return $img['tmp_name'];
        }, $request->getFiles()->toArray());

        $imagem['href'] = $request->getPost()['href'];
        $imagem['target'] = $request->getPost()['target'];
        $imagem['idCadastro'] = $dadosSiteHospedado[0]['idCadastro'];
        $imagem['idSiteHospedadoBanner'] = 0;

        $imagem[$apiClient::KEY_FILES] = [
            'fotos' => $arrayFotos
        ];

        // Envia o banner
        $retorno = $this->getApiClient()->SiteHospedadoBannerPost($imagem);

        return $this->redirect()->toUrl('/banners');
    }


    public function excluirAction()
    {
        $key = $this->getRequest()->getPost()['key'];
        $idSiteHospedadoBanner = $this->getRequest()->getPost()['idSiteHospedadoBanner'];

        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        // deleta o banner
        $retorno = $this->getApiClient()->SiteHospedadoBannerDelete([
            'idSiteHospedado' => $dadosSiteHospedado[0]['idSiteHospedado'],
            'idCadastro' => $dadosSiteHospedado[0]['idCadastro'],
            'idSiteHospedadoBanner' => $idSiteHospedadoBanner,
            'key' => $key,
        ])->json();

        return $this->redirect()->toUrl('/banners');
    }
}
