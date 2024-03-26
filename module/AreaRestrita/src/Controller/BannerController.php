<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\SiteHospedado;
use Laminas\Http\Response;
use Laminas\View\Model\ViewModel;

class BannerController extends AbstractActionController
{
    /**
     * Listagem dos banners ja cadastrados
     */
    public function indexAction(): ViewModel
    {
        $banners = [];

        /** @var SiteHospedado $siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        $dadosSite = $dadosSiteHospedado[0]['idSiteHospedado'] ?? null;

        if ($dadosSite) {
            $banners = $this->getApiClient()
                ->SiteHospedadoBannerGet(['idSiteHospedado' => $dadosSite])
                ->json();
        }

        return new ViewModel([
            'banners' => $banners['dados'] ?? [],
            'siteHospedado' => $dadosSiteHospedado,
        ]);
    }

    /**
     * View de cadastro de banners
     */
    public function cadastrarAction(): ViewModel
    {
        return new ViewModel();
    }

    /**
     * @return Response
     */
    public function salvarAction()
    {
        $request = $this->getRequest();
        $apiClient = $this->getApiClient();

        /** @var SiteHospedado $siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        if (!isset($dadosSiteHospedado[0])) {
            return $this->redirect()->toUrl('/banners');
        }

        $arrayFotos = array_map(function ($img) {
            return $img['tmp_name'];
        }, $request->getFiles()->toArray());

        $imagem['href'] = $request->getPost()['href'];
        $imagem['target'] = $request->getPost()['target'];
        $imagem['idCadastro'] = $dadosSiteHospedado[0]['idCadastro'];
        $imagem['idSiteHospedadoBanner'] = 0;

        $imagem[$apiClient::KEY_FILES] = [
            'fotos' => $arrayFotos,
        ];

        // Envia o banner
        $this->getApiClient()->SiteHospedadoBannerPost($imagem);

        return $this->redirect()->toUrl('/banners');
    }

    /**
     * @return Response
     */
    public function excluirAction()
    {
        $key = $this->getRequest()->getPost()['key'];
        $idSiteHospedadoBanner = $this->getRequest()->getPost()['idSiteHospedadoBanner'];

        /** @var SiteHospedado $siteHospedado */
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
