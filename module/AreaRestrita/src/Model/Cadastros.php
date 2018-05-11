<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\Cadastros as ApiModelCadastros;

class Cadastros extends ApiModelCadastros
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
}
