<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\AbstractModel;

class Estados extends AbstractModel
{

    use Traits\TraitIdentity;

    public function get(array $data)
    {
        return parent::get($data)->getData();
    }
}
