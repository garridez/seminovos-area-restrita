<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\PlanosRevenda as ApiModelPagamentos;

class Planos extends ApiModelPagamentos
{

    use Traits\TraitIdentity;

    /**
     * Retorna os dados do usuário logado
     * 
     * @param bool $cacheable Determina se os dados vão vim do cache ou não
     * @return array
     */
    public function getCurrent($cacheable = true)
    {
        return parent::get([], $this->getIdentity(), $cacheable)->getData()[0];
    }

    public function get($tipoPlano = null)
    {
        return parent::get([
            'tipoPlano' => $tipoPlano
        ])->getData();

    }
}
