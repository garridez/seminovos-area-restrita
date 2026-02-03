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
				mkdir($tempDir, 0775, true);
			}
            $moveUpload = new MoveUpload([
                'target' => $tempDir,
                'overwrite' => true,
                'randomize' => true,
                'use_upload_name' => true,
                'use_upload_extension' => true,
            ]);

            $fotos = $request->getFiles()->fotos;
			
			if (empty($fotos)) {
				return new JsonModel([
					'status' => 400,
					'detail' => 'Nenhuma foto enviada',
				]);
			}			
			
			// primeiro isso
			if (isset($fotos['tmp_name'])) {
				return new JsonModel([
					'status' => 405,
					'detail' => 'O campo fotos deve ser um array.',
				]);
			}			
			
			$maxSize = 5 * 1024 * 1024; // 5 MB
			$allowedTypes = ['image/jpeg', 'image/png'];

			foreach ($fotos as $foto) {
				if ($foto['error'] !== UPLOAD_ERR_OK) {
					return new JsonModel([
						'status' => 400,
						'detail' => 'Erro no upload da foto: '.$foto['name'],
					]);
				}

				if ($foto['size'] > $maxSize) {
					return new JsonModel([
						'status' => 400,
						'detail' => 'Foto muito grande (máx 5MB): '.$foto['name'],
					]);
				}

				if (!in_array($foto['type'], $allowedTypes)) {
					return new JsonModel([
						'status' => 400,
						'detail' => 'Formato inválido: '.$foto['name'],
					]);
				}
			}

            // Upload
            if ($fotos) {
                $fotosVeiculo = $this->getApiClient()
                        ->veiculosFotosGet(['idVeiculo' => $dataPost->idVeiculo])
                        ->json();
                $ultimoOrdem = is_countable($fotosVeiculo['data']) ? count($fotosVeiculo['data']) : 0;

				$totalFotos = count($fotosVeiculo['data']) + count($fotos);

				if ($totalFotos > 15) {
					return new JsonModel([
						'status' => 400,
						'detail' => 'Limite máximo de 15 fotos por veículo',
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
				
				if (!is_array($files) || count($files) === 0) {
					return new JsonModel([
						'status' => 500,
						'detail' => 'Falha ao mover os arquivos',
					]);
				}

				if (count($files) !== count($fotos)) {
					return new JsonModel([
						'status' => 500,
						'detail' => 'Nem todas as fotos foram processadas',
					]);
				}				
				
                $data[$apiClient::KEY_FILES] = [
                    'fotos' => $files,
                ];

                $resUpload = $this->getApiClient()->veiculosFotosPost($data)->json();

                foreach ($files as $file) {
                    unlink($file);
                }

				if (
					!isset($resUpload['status']) ||
					$resUpload['status'] !== 200 ||
					empty($resUpload['data'])
				) {
					return new JsonModel([
						'status' => 502,
						'detail' => 'API não confirmou salvamento das fotos',
					]);
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
