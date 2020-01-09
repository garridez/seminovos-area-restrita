<?php


namespace AreaRestrita\Model;

use Aws\Athena\AthenaClient;
use Aws\Athena\Exception\AthenaException;
use Aws\Exception\AwsException;
use SnBH\ApiModel\Model\AbstractModel;

class Contador
{

    static private $ACCESS_KEY = 'AKIAQTEY4VWFXSY66MHD';
    static private $SECRET_KEY = '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';   
    private $athenaClient;
    private $query;
    
    public function __construct()
    {
        $this->athenaClient = AthenaClient::factory([
            'version' => '2017-05-18',
            'region' => 'us-west-2',
            'credentials' => [
                'key' => $this::$ACCESS_KEY,
                'secret' => $this::$SECRET_KEY
            ]
        ]);
        
    }


    public function gerarQueryAcesso($tabela, $campos, $idCadastro= null, $granularidade= null, $idsVeiculos =[], $orderBy = 'contador', $limit= null )
    {
        $campos = (count($campos) != 0) ? $campos : [
            'idVeiculo',
            'marca',
            'modelo',
            'categoria',
        ];

        //trada idCadastro
        $where = '';
        if($idCadastro) {
            $where = "where idcadastro = $idCadastro";
            $campos[] = 'idCadastro';
        }
        
        //trata os dados dos campos
        $stringCampos = '';
        foreach($campos as $campo){
            $stringCampos .= $campo . ',';
        }
        $stringCampos = substr($stringCampos, 0, -1); 
        $groupBy = $stringCampos;

        //trata idsVeiculos
        if( isset($idsVeiculos) && count($idsVeiculos)) {
            $where = $where === ''? '' : $where. ' and ';
            $ids = implode(', ', $idsVeiculos); 
            $where .= "idveiculo in ($ids)";
        }


        //trata granularidade
        if($granularidade) {
            $stringCampos .= ", $granularidade as data";
            $groupBy .= ", $granularidade";
        }

        if($limit) {
            $limit = 'limit ' . $limit;
        }

        $this->query =  <<< "sql"
        SELECT 
            $stringCampos,
            count(1) AS contador
        FROM $tabela
            $where
        GROUP BY  $groupBy
        ORDER BY $orderBy desc
        $limit
sql;
    }

    public function getDados($idCadastro = null)
    {
        try {
            
            $result = $this->athenaClient->startQueryExecution([
                'QueryExecutionContext' => ['Database' => 'contador'],
                'QueryString' => $this->query,
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
        
        $this->dadosContador = $this->processarResultado($res);
        
        return $this->dadosContador;        
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

    private function processarResultado($res)
    {
        $result = array();
        $resul_array = array();
        
        // echo '@@@Count: '.count($res).'<br/>';
        
        for ($i = 0; $i < count($res); $i++) {
            for ($n = 0; $n < count($res[$i]['Data']); $n++) {
                if ($i == 0)
                    $result[] = $res[$i]['Data'][$n]['VarCharValue'];
                else {
                    $resul_array[$i][$result[$n]] = $res[$i]['Data'][$n]['VarCharValue'] ?? '';
                }
            }
        }
        
        // echo 'resul_array_cnt: '.count($resul_array).'<br/>';
        return $resul_array;
    }
    
}