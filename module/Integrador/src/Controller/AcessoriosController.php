<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

class AcessoriosController extends AbstractActionController {

    public function fetch() {

        $request = $this->request;
        $idCadastro = $this->getIdCadastro();

        $idTipo = $this->params()->fromQuery('tipo');

        $res = $this->getApiClient()->acessorios([
                'idTipo' => $idTipo
                ], null, 10000)->json();

        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        return new JsonModel($res);
    }

}
