<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectMarca extends Select
{

    public function __construct($name = 'idMarca', $options = array())
    {
        $options = array_merge([
            'label' => 'Marca',
            ], $options);

        parent::__construct($name, $options);
    }
}
