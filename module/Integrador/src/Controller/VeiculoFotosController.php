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

                $fotosVeiculo = $this->getApiClient()
                        ->veiculosFotosGet(['idVeiculo' => $dataPost->idVeiculo])
                        ->json();
                $ultimoOrdem = sizeof($fotosVeiculo['data']);
                
                if($ultimoOrdem == 12){
                    return new JsonModel([
                        'staus'=> 405,
                        'detail' => 'Limite de fotos alcançado'
                        ]);
                }
                
                foreach($fotos as $foto){
                    $ordem[] = $ultimoOrdem + 1;
                    $ultimoOrdem++;
                }

                $data = [
                    'idTipo' => 1,
                    'idVeiculo' => $dataPost->idVeiculo,
                    'ordem' => $ordem,
                ];                

                $files = $moveUpload->move($fotos, true);
                $data[$apiClient::KEY_FILES] = [
                    'fotos' => $files
                ];

                $resUpload = $this->getApiClient()->veiculosFotosPost($data)->json();

                foreach ($files as $file) {
                    unlink($file);
                }
                
                if($resUpload['status'] !== 200){
                    return new JsonModel($resUpload);
                }

            }

            $dataJson = [
                'status' => 200,
                'data' => $resUpload['data']
            ];

            return new JsonModel($dataJson);
        }
    }
    
    public function delete() {
        $idFoto = $this->params('id');
        $idVeiculo = $this->params()->fromQuery('idVeiculo');
        
        /* @var $veiculosFotosModel VeiculosFotos */
        $veiculosFotosModel = $this->getContainer()->get(VeiculosFotos::class);

        #deletar fotos do servidor
        $retorno = $veiculosFotosModel->delete(['listaFotos' => [$idFoto]]);

        if($retorno['status'] !== 200){
            return new JsonModel($retorno);
        }
        
        $fotosVeiculo = $this->getApiClient()
                        ->veiculosFotosGet(['idVeiculo' => $idVeiculo])
                        ->json();
        
        $auxOrdem = 1;
        foreach($fotosVeiculo['data'] as $foto){
                    $ordem[$auxOrdem] = $foto['idFoto'];
                    $auxOrdem++;
        }
        
        $resReordem = $this->getApiClient()->veiculosFotosPut([
                    'reordem' => $ordem,
                    'metadata' => [
                        'idVeiculo' => $idVeiculo
                    ],
                ])->json();

        if($resReordem['status'] !== 200){
            return new JsonModel($retorno);
        }
        
        $dataJson = [
                'status' => 200,
                'detail' => 'Foto deletada com sucesso.'
            ];

        return new JsonModel($dataJson);
        
    }
}
