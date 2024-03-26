<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\Cadastros as ApiModelCidades;

class Cidades extends ApiModelCidades
{
    use Traits\TraitIdentity;

    /**
     * @return array
     */
    public function get(array $data)
    {
        return parent::get($data)->getData();
    }
}
