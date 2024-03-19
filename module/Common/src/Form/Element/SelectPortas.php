<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\Select;

class SelectPortas extends Select
{
    /** @var array */
    protected $valueOptions = [
        '' => 'Selecione',
        1 => '1 Porta',
        2 => '2 Portas',
        3 => '3 Portas',
        4 => '4 Portas',
        5 => '5 Portas',
        6 => '6 Portas',
    ];

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'carroPortas', $options = [])
    {
        $options = array_merge([
            'label' => 'Portas',
            'name' => 'carroPortas',
        ], $options);

        parent::__construct($name, $options);
    }
}
