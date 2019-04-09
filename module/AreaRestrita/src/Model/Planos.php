<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\Cadastros;
use SnBH\ApiModel\Model\PlanosRevenda as ApiModelPagamentos;

class Planos extends ApiModelPagamentos
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

    public function get($tipoPlano = null, $cache = true)
    {
        return parent::get([
                'tipoPlano' => $tipoPlano
                ], null, $cache)->getData();
    }
}
