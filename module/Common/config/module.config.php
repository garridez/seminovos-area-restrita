<?php

use SnBH\Common\Form\Element;

return [
    'form_elements' => [
        'factories' => [
            Element\SelectCidades::class => Element\Factory\SelectCidadeFactory::class,
            Element\SelectMarca::class => Element\Factory\SelectMarcaFactory::class,
        ],
    ],
];
