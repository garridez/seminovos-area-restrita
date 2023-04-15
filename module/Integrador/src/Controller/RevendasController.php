<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\JsonModel;

class RevendasController extends AbstractActionController {

    public function create()
    {
        $request = $this->request;
        $data = $request->getPost()->toArray();
        
        $requerid = [
            'cnpj',
            'razaoSocial',
            'nomeFantasia',
            'telefone_2',
            'email',
            'responsavelNome',
            'cpfResponsavel',
        ];
        $diff = array_diff($requerid, array_keys($data));
        if (!empty($diff)) {
             return new JsonModel(['status' => 401, 'detail' =>"Os campos: '" . implode(', ', $diff) . "' são obrigatórios!"]);
        }
        
        if ($this->validarCnpjAction($data['cnpj'])) {
            return new JsonModel([
                'status' => 401,
                'detail' => 'CNPJ cadastrado junto a Seminovos',
            ]);
        }
        
         $variablesDefault = [
            'tipoCadastro'      => 1,
            'razaoSocial'       => '',
            'nomeFantasia'      => '',
            'responsavelNome'   => '',
            'cnpj'              => '',
            'cpfResponsavel'    => '',
            'logradouro'        => '',
            'numero'            => '',
            'complemento'       => '',
            'bairro'            => '',
            'cep'               => '',
            'idCidade'          => '2707',
            'idEstado'          => '11',
            'cidade'            => 'Belo Horizonte',
            'estado'            => 'MG',
            'telefone_2'        => '',
            'telefone_3'        => null,
            'telefone_1'        => '',
            'telefone_4'        => null,
            'operadora_1'       => null,
            'operadora_2'       => null,
            'operadora_3'       => null,
            'operadora_4'       => null,
            'telefone_1_is_wpp' => null,
            'telefone_2_is_wpp' => null,
            'telefone_3_is_wpp' => null,
            'telefone_4_is_wpp' => null,
            'site'              => null,
            'email'             => '',
            'email_secundario'  => null,
            'email_financeiro'  => '',
            'dataExpiracao'     => date('Y-m-d', strtotime('+1 year')),
            'diaPagamento'      => '',
            'valorPlano'        => '',
            'mapa'              => '',
            'observacoes'       => 'Revenda cadastrada via Autoconecta',
            'dataCadastro'      => date('Y-m-d'),
            'idFilial'          => 1,
            'flag_pendencia'    => null,
            'idPlano'           => 30,
            'simples'           => 10,
            'turbo'             => 10,
            'nitro'             => 10,
            'username'          => self::transformLabel($data['nomeFantasia']),
            'origem'            => 'externo'
        ];
        $dados = array_merge($variablesDefault, $data);
        
        
        //var_dump($this->getApiClient()->cadastrosPost($dados)->getBody()); exit;

        $res = $this->getApiClient()->cadastrosPost($dados)->json();

        if ($res['status'] !== 200) {
            return new JsonModel($res);
        }

        return new JsonModel($res);
    }
    
    public function validarCnpjAction($cnpj)
    {
        $cadastro = $this->getApiClient()->cadastrosGet(['cnpj' => $cnpj, 'considerarInativo' => true], null, false)->getData();
        
        return isset($cadastro[0]['idCadastro']) && $cadastro[0]['idCadastro'] ? true : false;
    }
    
    protected function transformLabel($string, $tolower = true)
    {
        $string = preg_replace("`\[.*\]`U","",(string) $string);
        $string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
        // Remove acentos
        $string = preg_replace(
            ["/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"],
            explode(" ","a A e E i I o O u U n N"),$string);
        // $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
        if ($tolower) {
            $string = preg_replace( ["`[^a-z0-9]`i", "`[-]+`"] , "-", $string);
            $string = strtolower(trim($string, '-'));
        }
        return $string;
    }

}
