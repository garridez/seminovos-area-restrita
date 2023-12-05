<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\Cadastros as ApiModelCidades;

class Cidades extends ApiModelCidades
{

    use Traits\TraitIdentity;

    public function get(array $data)
    {
        return parent::get($data)->getData();
    }
}
