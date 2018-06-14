<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectCombustivel extends Select
{

    protected $valueOptions = [
        '' => 'Selecione',
        1 => 'Álcool',
        2 => 'Bi-Combustível',
        3 => 'Diesel',
        4 => 'Gasolina',
        5 => 'Gasolina + Kit Gás',
        6 => 'Kit Gás',
        7 => 'Tetra Fuel',
    ];

    public function __construct($name = 'idCombustivel', $options = array())
    {
        $options = array_merge([
            'label' => 'Combustível',
            'name' => 'idCombustivel'
            ], $options);

        parent::__construct($name, $options);
    }
}
