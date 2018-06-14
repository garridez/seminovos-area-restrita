<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\MultiCheckbox;

class CheckboxAcessorios extends MultiCheckbox
{

    protected $valueOptions = [
        1 => 'ABS',
        4 => 'ALARME',
        6 => 'AR CONDICIONADO',
        17 => 'DIREÇÃO HIDRÁULICA',
        22 => 'MP3 / USB',
        30 => 'TETO-SOLAR',
    ];

    public function __construct($name = 'acessorios', $options = array())
    {
        $options = array_merge([
            'label' => 'Acessórios',
            'name' => 'acessorios'
            ], $options);

        parent::__construct($name, $options);
    }
}
