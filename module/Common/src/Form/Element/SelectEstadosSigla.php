<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectEstadosSigla extends Select
{

    protected $valueOptions = [
        '' => 'Selecione',
        1 => 'AC',
        2 => 'AL',
        3 => 'AM',
        4 => 'AP',
        5 => 'BA',
        6 => 'CE',
        7 => 'DF',
        8 => 'ES',
        9 => 'GO',
        10 => 'MA',
        11 => 'MG',
        12 => 'MS',
        13 => 'MT',
        14 => 'PA',
        15 => 'PB',
        16 => 'PE',
        17 => 'PI',
        18 => 'PR',
        19 => 'RJ',
        20 => 'RN',
        21 => 'RO',
        22 => 'RR',
        23 => 'RS',
        24 => 'SC',
        25 => 'SE',
        26 => 'SP',
        27 => 'TO',
    ];

    public function __construct($name = 'idEstado', $options = array())
    {
        $options = array_merge([
            'label' => 'Estado',
            'name' => 'idEstado'
            ], $options);

        parent::__construct($name, $options);
    }
}
