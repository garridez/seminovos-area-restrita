<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use Zend\View\Model\ViewModel;
use Aws\Athena\AthenaClient;
use Aws\Athena\Exception\AthenaException;
use Aws\Exception\AwsException;
use Exception;

class PainelController extends AbstractActionController
{
    private $athenaClient;
    static private $ACCESS_KEY = 'AKIAQTEY4VWFXSY66MHD';
    static private $SECRET_KEY = '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';   

    private function retornarQueryAcessoPorDia($idCadastro)
    {
        return <<< sql
        SELECT idcadastro,
            idveiculo,
            DATE(from_unixtime(time)) AS date,
            count(1) AS contador
        FROM acesso
        WHERE idcadastro = $idCadastro
        GROUP BY  idCadastro, idVeiculo, DATE(from_unixtime(time)) limit 1
sql;
    }

    public function indexAction()
    {
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);
        $cadastro = $cadastrosModel->getCurrent();
        
        //echo $this->retornarQueryAcessoPorDia($cadastro['idCadastro']);
        //die;
        ini_set('xdebug.var_display_max_depth', '10');
        ini_set('xdebug.var_display_max_children', '256');
        ini_set('xdebug.var_display_max_data', '1024');

        try {
            $this->athenaClient = AthenaClient::factory([
                'version' => '2017-05-18',
                'region' => 'us-west-2',
                'credentials' => [
                    'key' => $this::$ACCESS_KEY,
                    'secret' => $this::$SECRET_KEY
                ]
            ]);

            $result = $this->athenaClient->startQueryExecution([
                'QueryExecutionContext' => ['Database' => 'contador'],
                'QueryString' => $this->retornarQueryAcessoPorDia($cadastro['idCadastro']),
                'ResultConfiguration' => [
                    //'EncryptionOption' => 'SSE_S3',
                    'OutputLocation' => "s3://aws-athena-query-results-041122835851-us-west-2/Unsaved/"
                ]
            ]);
        } catch (AthenaException $e) {
            echo $e->getMessage();die;
        } catch (AwsException $e) {
            echo $e->getMessage();die;
        }

        $QueryExecutionId = $result->get('QueryExecutionId');

        $this->esperarCompletarQuery($QueryExecutionId);

        $result1 = $this->athenaClient->GetQueryResults(array(
            'QueryExecutionId' => $QueryExecutionId, // REQUIRED
            'MaxResults' => 500
        ));

        $data = $result1->get('ResultSet');
            $res  = $data['Rows'];
            
            while (true) {
                
                if ($result1->get('NextToken') == null) {
                    break;
                }
                
                $result1 = $this->Client->GetQueryResults(array(
                    'QueryExecutionId' => $QueryExecutionId, // REQUIRED
                    'NextToken' => $result1->get('NextToken'),
                    'MaxResults' => 500
                ));
                
                $data = $result1->get('ResultSet');
                $res  = array_merge($res, $data['Rows']);
                ;
            }
            
        $acessosVeiculosPorDia = $this->processResultRows($res);
        var_dump($acessosVeiculosPorDia);die;

        //return new ViewModel(compact('acessosVeiculosPorDia'));
    }

    private function esperarCompletarQuery($QueryExecutionId)
    {
        while (1) {
            $result = $this->athenaClient->getQueryExecution(array(
                'QueryExecutionId' => $QueryExecutionId
            ));
            $res = $result->toArray();
            
            //echo $res['QueryExecution']['Status']['State'].'<br/>';
            if ($res['QueryExecution']['Status']['State'] == 'FAILED') {
                echo "Query Failed";
                die;
            } else if ($res['QueryExecution']['Status']['State'] == 'CANCELED') {
                echo "Query was cancelled";
                die;
            } else if ($res['QueryExecution']['Status']['State'] == 'SUCCEEDED') {
                break; // break while loop
            }
        }
    }   

    function processResultRows($res)
    {
        $result = array();
        $resul_array = array();
        
        // echo '@@@Count: '.count($res).'<br/>';
        
        for ($i = 0; $i < count($res); $i++) {
            for ($n = 0; $n < count($res[$i]['Data']); $n++) {
                if ($i == 0)
                    $result[] = $res[$i]['Data'][$n]['VarCharValue'];
                else {
                    $resul_array[$i][$result[$n]] = $res[$i]['Data'][$n]['VarCharValue'];
                }
            }
        }
        
        // echo 'resul_array_cnt: '.count($resul_array).'<br/>';
        return $resul_array;
    }
}
