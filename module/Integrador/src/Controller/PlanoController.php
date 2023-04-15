<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use SnBH\Common\Helper\MoveUpload;
use Laminas\Mvc\MvcEvent;
use SnBH\Common\ServiceVeiculo;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Model\VeiculosFotos;
use Laminas\View\Model\JsonModel;

class PlanoController extends AbstractActionController {

    public function fetch() {

        $request = $this->request;
        $idCadastro = $this->getIdCadastro();

        $res = $this->getApiClient()->planosGet([
                    'idCadastro' => $idCadastro,
                    'tipoCadastro' => 1,
                        ], 'anuncios')
                ->json();
        
        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        $data = [
          'totalBasico' => $res['data'][0]['totalBasico'],
          'totalBasicoPublicados' => $res['data'][0]['totalBasicoPublicado'],
          'totalTurbo' => $res['data'][0]['totalTurbo'],
          'totalTurboPublicados' => $res['data'][0]['totalTurboPublicados'],
          'totalNitro' => $res['data'][0]['totalNitro'],
          'totalNitroPublicados' => $res['data'][0]['totalNitroPublicados'],
          'totalAnuncios' => $res['data'][0]['totalAnuncios'],
          'totalAnunciosPublicados' => $res['data'][0]['totalAnunciosPublicados']
        ];

        $dataJson = [
            'status' => 200,
            'data' => $data
        ];

        return new JsonModel($dataJson);
    }

}
