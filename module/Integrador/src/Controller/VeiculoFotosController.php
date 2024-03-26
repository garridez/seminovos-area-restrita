<?php

namespace SnBH\Integrador\Controller;

use AreaRestrita\Model\VeiculosFotos;
use Laminas\View\Model\JsonModel;
use SnBH\Common\Helper\MoveUpload;

class VeiculoFotosController extends AbstractActionController
{
    public function create()
    {
        ini_set('memory_limit', '256M');

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

            // Se existe $fotos['tmp_name'] quer dizer que não é um array de fotos
            if (isset($fotos['tmp_name'])) {
                return new JsonModel([
                    'status' => 405,
                    'detail' => 'O campo fotos deve ser um array.',
                ]);
            }

            // Upload
            if ($fotos) {
                $fotosVeiculo = $this->getApiClient()
                        ->veiculosFotosGet(['idVeiculo' => $dataPost->idVeiculo])
                        ->json();
                $ultimoOrdem = is_countable($fotosVeiculo['data']) ? count($fotosVeiculo['data']) : 0;

                if ($ultimoOrdem == 15) {
                    return new JsonModel([
                        'status' => 405,
                        'detail' => 'Limite de fotos alcançado',
                    ]);
                }

                foreach ($fotos as $foto) {
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
                    'fotos' => $files,
                ];

                $resUpload = $this->getApiClient()->veiculosFotosPost($data)->json();

                foreach ($files as $file) {
                    unlink($file);
                }

                if ($resUpload['status'] !== 200) {
                    return new JsonModel($resUpload);
                }
            } else {
                return new JsonModel([
                    'status' => 405,
                    'detail' => 'Formato invalido para as fotos enviadas',
                ]);
            }

            $dataJson = [
                'status' => 200,
                'data' => $resUpload['data'] ?? $resUpload,
            ];

            return new JsonModel($dataJson);
        }
    }

    public function delete()
    {
        $idFoto = $this->params('id');
        $idVeiculo = $this->params()->fromQuery('idVeiculo');

        /** @var VeiculosFotos $veiculosFotosModel */
        $veiculosFotosModel = $this->getContainer()->get(VeiculosFotos::class);

        // deletar fotos do servidor
        $retorno = $veiculosFotosModel->delete(['listaFotos' => [$idFoto]]);

        if ($retorno['status'] !== 200) {
            return new JsonModel($retorno);
        }
        $ordem = [];

        $fotosVeiculo = $this->getApiClient()
                        ->veiculosFotosGet(['idVeiculo' => $idVeiculo])
                        ->json();

        $auxOrdem = 1;
        foreach ($fotosVeiculo['data'] as $foto) {
                    $ordem[$auxOrdem] = $foto['idFoto'];
                    $auxOrdem++;
        }

        $resReordem = $this->getApiClient()->veiculosFotosPut([
            'reordem' => $ordem,
            'metadata' => [
                'idVeiculo' => $idVeiculo,
            ],
        ])->json();

        if ($resReordem['status'] !== 200) {
            return new JsonModel($retorno);
        }

        $dataJson = [
            'status' => 200,
            'detail' => 'Foto deletada com sucesso.',
        ];

        return new JsonModel($dataJson);
    }
}
