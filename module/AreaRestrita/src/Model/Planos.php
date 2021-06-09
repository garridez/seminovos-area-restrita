<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\AbstractModel;
use SnBH\ApiModel\Model\Cadastros;

class Planos extends AbstractModel
{

    use Traits\TraitCadastro;
    use Traits\TraitIdentity;

    /**
     * Retorna os planos de acordo com o tipo de usuário atual
     * 
     * @param bool $cacheable Determina se os dados vão vim do cache ou não
     * @return array
     */
    public function getCurrent($cacheable = true)
    {
        $cadastro = $this->getCadastroData();
        if (!$cadastro) {
            return false;
        }

        return parent::get([
                'tipoPlano' => Cadastros::TIPO_ID_STRING[$cadastro['tipoCadastro']]
                ], null, $cacheable)->getData();
    }

    public function get($tipoPlano = null, $cache = true, $status = 1)
    {
        if (!is_numeric($tipoPlano)) {
            $tipoPlano = Cadastros::TIPO_STRING_ID[strtolower($tipoPlano)];
        }
        $tipoPlano = Cadastros::TIPO_ID_STRING[(int) $tipoPlano];

        if($tipoPlano === 'revenda'){
            $status = -1;
        }

        return parent::get([
                'tipoPlano' => $tipoPlano,
                'status' => $status
                ], null, null)->getData();
    }

    public function getPlanosUsados($cacheable = false)
    {
        $cadastro = $this->getCadastroData();

        if (!$cadastro) {
            return false;
        }

        return parent::get([
                'tipoCadastro' => $cadastro['tipoCadastro'],
                'idCadastro' => $cadastro['idCadastro'],
                'version2' => 1,
                ], 'anuncios', $cacheable)->getData();
    }
}
