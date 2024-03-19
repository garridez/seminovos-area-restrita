<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\Select;

class SelectCidades extends Select
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'idCidade', $options = [])
    {
        $options = array_merge([
            'label' => 'Cidade',
        ], $options);

        parent::__construct($name, $options);
    }
}
