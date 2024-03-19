<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\Select;

class SelectCor extends Select
{
    /** @var array */
    protected $valueOptions = [
        '' => 'Selecione',
        //        'Prata' => 'Prata',
        //        'Preto' => 'Preto',
        //        'Branco' => 'Branco',
        //        'Cinza' => 'Cinza',
        //        'Vermelho' => 'Vermelho',
        //        '' => '',
        'Amarelo' => 'Amarelo',
        'Azul' => 'Azul',
        'Bege' => 'Bege',
        'Branco' => 'Branco',
        'Bronze' => 'Bronze',
        'Cinza' => 'Cinza',
        'Dourado' => 'Dourado',
        'Laranja' => 'Laranja',
        'Marrom' => 'Marrom',
        'Prata' => 'Prata',
        'Preto' => 'Preto',
        'Rosa' => 'Rosa',
        'Roxo' => 'Roxo',
        'Verde' => 'Verde',
        'Vermelho' => 'Vermelho',
        'Vinho' => 'Vinho',
    ];

    /** @var null|string|array */
    protected $emptyOption = 'Selecione';

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'cor', $options = [])
    {
        $options = array_merge([
            'label' => 'Cor',
            'name' => 'cor',
        ], $options);

        parent::__construct($name, $options);
    }
}
