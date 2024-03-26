<?php

namespace SnBH\Integrador\Controller;

use Laminas\View\Model\JsonModel;

class MarcasController extends AbstractActionController
{
    public function fetch()
    {
        //$request = $this->request;
        //$idCadastro = $this->getIdCadastro();

        $idTipo = $this->params()->fromQuery('tipo');

        $res = $this->getApiClient()->marcas([
            'idTipo' => $idTipo,
        ], null, 10000)->json();

        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        return new JsonModel($res);
    }
}
