<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectCidades extends Select
{

    public function __construct($name = 'idCidade', $options = [])
    {
        $options = array_merge([
            'label' => 'Cidade',
            ], $options);

        parent::__construct($name, $options);
    }
}
