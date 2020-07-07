<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class ModelosController extends AbstractActionController {

    public function fetch() {

        $request = $this->request;
        $idCadastro = $this->getIdCadastro();

        $idMarca = $this->params()->fromQuery('idMarca');

        if (!isset($idMarca) && !$idMarca || empty($idMarca)) {
            return new JsonModel([
                        'staus'=> 405,
                        'detail' => 'É obrigatório o paramêtro idMarca'
                        ]);
        }

        $res = $this->getApiClient()->modelos([
                'idMarca' => $idMarca
                ], null, 10000)->json();

        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        return new JsonModel($res);
    }

}
