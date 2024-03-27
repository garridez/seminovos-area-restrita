<?php

namespace AreaRestrita\Controller;

use Laminas\View\Model\ViewModel;

use Laminas\View\Model\JsonModel;

use \Laminas\Http\PhpEnvironment\Request;

class RepasseController extends AbstractActionController
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

        $curl = curl_init();

        $request = $this->request;

        $page = $request->getQuery('page') ?? 1;

        $cidade = $request->getQuery('city');

        $anoDe = $request->getQuery('anoDe');
        $anoAte = $request->getQuery('anoAte');
        $precoDe = $request->getQuery('precoDe');
        $precoAte = $request->getQuery('precoAte');

        $filtroMarca = $request->getQuery('search');

        $queryParams = [
            'page' => $page,
            'per_page' => 9,
            'city'=> $cidade,
            'search' => $filtroMarca,
            'price_min' => $precoDe,
            'price_max' => $precoAte,
            'year_to' => $anoDe,
            'year_from' => $anoAte,
            'status' => 1,
        ];

        
        $queryString = http_build_query($queryParams);
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://autoconecta.com.br/api/vehicles?' . $queryString,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $arr = json_decode($response, true);

        $veiculos = $arr['data'];
        $meta = $arr['meta'];

        /* @var $request \Laminas\Http\PhpEnvironment\Request */

        $routeName = str_replace("/repasse", "", (string) $request->getRequestUri());

        $routeParams = "/repasse";

        $paginationData = [
            'pages' => $meta['last_page'],
            'total' => $meta['total'],
            'current' => $page ?? 1,
            'routeName' => $routeName,
            'routeParams' => $routeParams,
            'pagination' => true,
            'paginationResultado' => true
        ];
        
        return new ViewModel([
            'parametro' => $this->params('parametro'),
            'veiculos' => $veiculos, 
            'paginationData' => $paginationData,
        ]);
    }
    public function anuncioAction(){

        return new ViewModel([
            'routeParams' => $this->routeParams,
        ]);
    }

    public function licensePlateAction(){

        $request = $this->request;

        $licensePlate = $request->getQuery('license-plate');

        if($licensePlate){
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://meus-anuncios.seminovos.com.br/integrador/veiculo?&placa='.$licensePlate,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => 'GET', 
                CURLOPT_HTTPHEADER => array(
                    'X-SnBH-Token: 879db53b62e90337D13316e85e81FaBe6f4943722090B568d6',
                    'X-SnBH-IdCadastro: 269236'
                ),
            ));
    
            $response = curl_exec($curl);
           $data = json_decode($response, true);
           return new JsonModel($data);
        }
    }
    public function meusAnunciosAction(){
        $curl = curl_init();

        $email = $this -> getCadastro('email');
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://autoconecta.com.br/api/vehicles?page=1&per_page=9&email=' . $email,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $arr = json_decode($response, true);

        $veiculos = $arr['data'];
        $meta = $arr['meta'];

        $request = $this->request;

        $routeName = str_replace("/repasse/meus-anuncios", "", (string) $request->getRequestUri());

        $routeParams = "/repasse/meus-anuncios";

        $paginationData = [
            'pages' => $meta['last_page'],
            'total' => $meta['total'],
            'current' => $page ?? 1,
            'routeName' => $routeName,
            'routeParams' => $routeParams,
            'pagination' => true,
            'paginationResultado' => true
        ];

        return new ViewModel([
            'routeParams' => $this->routeParams,
            'meta' => $meta,
            'veiculos' => $veiculos,
            'paginationData'=> $paginationData,
        ]);
    }
    public function editarAction(){
        $idVeiculo = $this->params('idRepasse');

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://autoconecta.com.br/api/vehicles/' . $idVeiculo,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $arr = json_decode($response, true);

        return new ViewModel([
            'routeParams' => $this->routeParams,
            'veiculo' => $arr,
        ]);
    }
}