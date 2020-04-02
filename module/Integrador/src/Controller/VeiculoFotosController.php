<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use SnBH\Common\Helper\MoveUpload;
use Zend\Mvc\MvcEvent;
use SnBH\Common\ServiceVeiculo;
use AreaRestrita\Model\Veiculos;
use AreaRestrita\Model\VeiculosFotos;
use Zend\View\Model\JsonModel;

class VeiculoFotosController extends AbstractActionController {


    public function create() {

        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->request;
        if ($request->isPost()) {
            $dataPost = $request->getPost();
            //var_dump($dataPost); exit;
            $apiClient = $this->getApiClient();
            $tempDir = $this->getContainer()->get('config')['dir']['upload'];
            $tempDir .= DIRECTORY_SEPARATOR . $dataPost->idVeiculo;
            if (!file_exists($tempDir)) {
                mkdir($tempDir);
            }
            $moveUpload = new MoveUpload([
                'target' => $tempDir,
                'overwrite' => true,
                'randomize' => true,
                'use_upload_name' => true,
                'use_upload_extension' => true,
            ]);
            
            $fotos = $request->getFiles()->fotos;
            // Upload
            if ($fotos) {
                $data = [
                    'idTipo' => 1,
                    'idVeiculo' => $dataPost->idVeiculo,
                        /* 'ordem' => $dataPost->ordem,
                          'rotacionar' => $dataPost->rotacionarNovasFotos, */
                ];                

                $files = $moveUpload->move($fotos, true);
                $data[$apiClient::KEY_FILES] = [
                    'fotos' => $files
                ];
                var_dump($data); //exit;
                $resUpload = $this->getApiClient()->veiculosFotosPost($data)->json();
                echo $resUpload;
                var_dump($resUpload); exit;
                foreach ($files as $file) {
                    unlink($file);
                }

                /* for ($i = 0; $i < sizeof($resUpload['data']['fotosInseridas']); $i++) {
                  $auxReordem[$dataPost->ordem[$i]] = $resUpload['data']['fotosInseridas'][$i];
                  } */
            }
            var_dump($resUpload);
            exit;

            $dataJson = [
                'status' => 200
            ];

            return new JsonModel($dataJson);
        }
    }
}
