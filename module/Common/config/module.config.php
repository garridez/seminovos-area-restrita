<?php

use SnBH\Common\Form\Element;
/**
 * @todo Fazer esses elementos funcionarem pelo "form_elements" e não pelo "service_manager"
 */
return [
    'service_manager' => [
        'factories' => [
            Element\SelectCidades::class => Element\Factory\SelectCidadeFactory::class,
            Element\SelectMarca::class => Element\Factory\SelectMarcaFactory::class,
            Element\CheckboxAcessorios::class => Element\Factory\CheckboxAcessoriosFactory::class,
        ]
    ],
    'form_elements' => [
        'factories' => [
            Element\SelectCidades::class => Element\Factory\SelectCidadeFactory::class,
            Element\SelectMarca::class => Element\Factory\SelectMarcaFactory::class,
        ],
    ],
];
