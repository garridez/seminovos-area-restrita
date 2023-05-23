<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SnBH\Integrador;

use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'integrador' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/integrador',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                        'middleware' => [
                            Middleware\TokenMiddleware::class,
                            Middleware\DispatchMiddleware::class,
                        ]
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'veiculo' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/veiculo[/:id]',
                            'defaults' => [
                                'controller' => Controller\VeiculoController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'veiculo-fotos' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/veiculo-fotos[/:id]',
                            'defaults' => [
                                'controller' => Controller\VeiculoFotosController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'planos' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/plano[/:id]',
                            'defaults' => [
                                'controller' => Controller\PlanoController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'marcas' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/marcas',
                            'defaults' => [
                                'controller' => Controller\MarcasController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'modelos' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/modelos',
                            'defaults' => [
                                'controller' => Controller\ModelosController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'acessorios' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/acessorios',
                            'defaults' => [
                                'controller' => Controller\AcessoriosController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'token' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/token',
                            'defaults' => [
                                'controller' => Controller\TokenController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'motor' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/motor',
                            'defaults' => [
                                'controller' => Controller\MotorController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'revendas' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/revendas',
                            'defaults' => [
                                'controller' => Controller\RevendasController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'estados' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/estados',
                            'defaults' => [
                                'controller' => Controller\EstadosController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'cidades' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/cidades',
                            'defaults' => [
                                'controller' => Controller\CidadesController::class,
                                'action' => 'dispatch',
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\VeiculoController::class => InvokableFactory::class,
            Controller\VeiculoFotosController::class => InvokableFactory::class,
            Controller\PlanoController::class => InvokableFactory::class,
            Controller\MarcasController::class => InvokableFactory::class,
            Controller\ModelosController::class => InvokableFactory::class,
            Controller\AcessoriosController::class => InvokableFactory::class,
            Controller\TokenController::class => InvokableFactory::class,
            Controller\MotorController::class => InvokableFactory::class,
            Controller\RevendasController::class => InvokableFactory::class,
            Controller\EstadosController::class => InvokableFactory::class,
            Controller\CidadesController::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            // Middleware
            Middleware\TokenMiddleware::class => Middleware\Factory\TokenMiddlewareFactory::class,
            Middleware\DispatchMiddleware::class => Middleware\Factory\MiddlewareGenericFactory::class,
        ]
    ],
];
