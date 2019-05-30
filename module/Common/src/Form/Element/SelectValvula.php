<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectValvula extends Select
{

    protected $valueOptions = [
        '' => 'Selecione',
        "3" => 8,
        "4" => 12,
        "1" => 16,
        "2" => 20,
        "5" => 24,
        "6" => 30,
        "7" => 32,
        "9" => 40,
        "8" => 48,
    ];

    public function __construct($name = 'idValvula', $options = array())
    {
        $options = array_merge([
            'label' => 'Válvula',
            'name' => 'idValvula'
        ], $options);

        parent::__construct($name, $options);
    }
}
