<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectModelo extends Select
{

    public function __construct($name = 'idModelo', $options = array())
    {
        $options = array_merge([
            'label' => 'Modelo',
            ], $options);

        parent::__construct($name, $options);
    }
}
