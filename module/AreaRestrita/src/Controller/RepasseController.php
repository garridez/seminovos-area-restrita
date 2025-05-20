<?php

namespace AreaRestrita\Controller;

use Laminas\View\Model\ViewModel;

use Laminas\View\Model\JsonModel;

use \Laminas\Http\PhpEnvironment\Request;
use SnBH\Common\Helper\MoveUpload;

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

        $res = $this->getApiClient()->repasseGet([
            'page' => $page
        ]);

        return new ViewModel([
            'parametro' => $this->params('parametro'),
            'veiculos' => $res->json(),
        ]);
    }
    public function anuncioAction()
    {
        $salvo = -1;
        if ($this->request->isPost()) {
            $tempDir = implode(DIRECTORY_SEPARATOR, [
                $this->getContainer()->get('config')['dir']['upload'],
                'repasse',
                uniqid(),
            ]);
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            $moveUpload = new MoveUpload([
                'target' => $tempDir,
                'overwrite' => true,
                'randomize' => true,
                'use_upload_name' => true,
                'use_upload_extension' => true,
            ]);
            $files = $moveUpload->move($this->request->getFiles()->fotos, true);
            $apiClient = $this->getApiClient();
            $post = $this->request->getPost();
            $dataPost = [
                ...$post,
                'idModelo' => $post['modeloCarro'],
                'idCadastro' => $this->getCadastro('idCadastro'),
                $apiClient::KEY_FILES => [
                    'fotos' => $files
                ]
            ];
            /** @var \SnBH\ApiClient\Response */
            $res = $apiClient->repassePost($dataPost);
            $salvo = $res->status === 200 ? 1 : 0;
        }

        return new ViewModel([
            'routeParams' => $this->routeParams,
            'salvo' => $salvo
        ]);
    }

    public function licensePlateAction()
    {

        $request = $this->request;

        $licensePlate = $request->getQuery('license-plate');

        if ($licensePlate) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://meus-anuncios.seminovos.com.br/integrador/veiculo?&placa=' . $licensePlate,
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
    public function meusAnunciosAction()
    {
        $res = $this->getApiClient()->repasseGet([
            'idCadastro' => $this->getCadastro('idCadastro'),
        ]);

        return new ViewModel([
            'routeParams' => $this->routeParams,
            'veiculos' => $res->json(),
        ]);
    }
    public function editarAction()
    {
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
    public function deletarAction()
    {
        $apiClient = $this->getApiClient();

        $repasseVeiculo = $apiClient->repasseGet(null, $this->params('idRepasse'));
        if ($repasseVeiculo->status !== 200) {
            echo json_encode([
                'status' => 404,
                'title' => 'Veículo não encontrado'
            ]);
            die;
        }
        $data = $repasseVeiculo->getData();
        if ($data['idCadastro'] !== $this->getCadastro('idCadastro')) {
            echo json_encode([
                'status' => 403,
                'title' => 'Não permitido'
            ]);
            die;
        }

        $res = $apiClient->repasseDelete(null,  $this->params('idRepasse'));
        echo json_encode($res->json());
        die;
    }
}
