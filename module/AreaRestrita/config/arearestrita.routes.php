<?php

/**
 * 
 * Todas rotas internas do sistema deve ficar aqui.
 * As rotas adicionadas aqui serão rotas filha da rota 'restrito'
 * Como a rota 'restrito' já possui um middleware de autenticação, todas as
 *  rotas filhas estão protegidas.
 * 
 * @see https://framework.zend.com/manual/2.4/en/modules/zend.mvc.routing.html
 * 
 */
use AreaRestrita\Controller as Ctrl;
use Zend\Router\Http;

return [
    'rota-exemplo' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'rota-exemplo',
            'defaults' => [
                'controller' => Ctrl\RotaExemploController::class,
                'action' => 'index',
                'parametro' => 'Valor Padrão do parametro'
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'parametro-opcional' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/[:parametro]'
                ],
            ],
            'sub-rota' => [
                'type' => Http\Literal::class,
                'options' => [
                    'route' => '/sub-rota',
                    'defaults' => [
                        'action' => 'sub-rota',
                    ],
                ],
            ],
            'outra-sub-rota' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/outra-sub-rota/:parametro-obrigatorio',
                    'defaults' => [
                        'action' => 'outra-sub-rota',
                    ],
                ],
            ],
        ],
    ],
    'meus-dados' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'meus-dados',
            'defaults' => [
                'controller' => Ctrl\MeusDadosController::class,
                'action' => 'index'
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'parametro-opcional' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/[:parametro]'
                ],
            ],
            'sub-rota' => [
                'type' => Http\Literal::class,
                'options' => [
                    'route' => '/sub-rota',
                    'defaults' => [
                        'action' => 'sub-rota',
                    ],
                ],
            ],
            'outra-sub-rota' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/outra-sub-rota/:parametro-obrigatorio',
                    'defaults' => [
                        'action' => 'outra-sub-rota',
                    ],
                ],
            ],
        ],
    ],
];
