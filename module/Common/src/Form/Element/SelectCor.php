<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectCor extends Select
{

    protected $valueOptions = [
        '' => 'Selecione',
        'Prata' => 'Prata',
        'Preto' => 'Preto',
        'Branco' => 'Branco',
        'Cinza' => 'Cinza',
        'Vermelho' => 'Vermelho',
        '' => '',
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
    protected $valuaaaeOptions = [
        [
            'label' => 'Prata',
            'value' => false,
        ],
        [
            'label' => 'Preto',
            'value' => '',
        ],
        [
            'label' => 'Branco',
            'value' => '',
        ],
        [
            'label' => 'Cinza',
            'value' => '',
        ],
        [
            'label' => 'Vermelho',
            'value' => '',
        ],
        [
            'label' => '',
            'value' => '',
        ],
        [
            'label' => 'Amarelo',
            'value' => '',
        ],
        [
            'label' => 'Azul',
            'value' => '',
        ],
        [
            'label' => 'Bege',
            'value' => '',
        ],
        [
            'label' => 'Branco',
            'value' => '',
        ],
        [
            'label' => 'Bronze',
            'value' => '',
        ],
        [
            'label' => 'Cinza',
            'value' => '',
        ],
        [
            'label' => 'Dourado',
            'value' => '',
        ],
        [
            'label' => 'Laranja',
            'value' => '',
        ],
        [
            'label' => 'Marrom',
            'value' => '',
        ],
        [
            'label' => 'Prata',
            'value' => '',
        ],
        [
            'label' => 'Preto',
            'value' => '',
        ],
        [
            'label' => 'Rosa',
            'value' => '',
        ],
        [
            'label' => 'Roxo',
            'value' => '',
        ],
        [
            'label' => 'Verde',
            'value' => '',
        ],
        [
            'label' => 'Vermelho',
            'value' => '',
        ],
        [
            'label' => 'Vinho',
            'value' => '',
        ],
    ];
    protected $emptyOption = 'Selecione';

    public function __construct($name = 'cor', $options = array())
    {
        $options = array_merge([
            'label' => 'Cor',
            'name' => 'cor'
            ], $options);

        parent::__construct($name, $options);
    }
}
