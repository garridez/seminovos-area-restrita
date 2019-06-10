<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\EnviarEmail as ApiModelEnviarEmail;

class EnviarEmail extends ApiModelEnviarEmail
{

    use Traits\TraitIdentity;

    public function post(array $data)
    {
        return parent::post($data)->getData();
    }
}
