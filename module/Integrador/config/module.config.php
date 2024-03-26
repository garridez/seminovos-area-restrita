<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace SnBH\Integrador;

use Laminas\Mvc\Middleware\PipeSpec;
use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;
use SnBH\Integrador\Middleware\TokenMiddleware;

return [
    'router' => [
        'routes' => [
            'integrador' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/integrador',
                    'defaults' => [
                        'controller' => PipeSpec::class,
                        'controllerHandler' => Controller\IndexController::class,
                        'action' => 'dispatch',
                        'middleware' => new PipeSpec(
                            TokenMiddleware::class,
                            Middleware\DispatchMiddleware::class
                        ),
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'veiculo' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/veiculo[/:id]',
                            'defaults' => [
                                'controllerHandler' => Controller\VeiculoController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'veiculo-fotos' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/veiculo-fotos[/:id]',
                            'defaults' => [
                                'controllerHandler' => Controller\VeiculoFotosController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'planos' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/plano[/:id]',
                            'defaults' => [
                                'controllerHandler' => Controller\PlanoController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'marcas' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/marcas',
                            'defaults' => [
                                'controllerHandler' => Controller\MarcasController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'modelos' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/modelos',
                            'defaults' => [
                                'controllerHandler' => Controller\ModelosController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'acessorios' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/acessorios',
                            'defaults' => [
                                'controllerHandler' => Controller\AcessoriosController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'token' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/token',
                            'defaults' => [
                                'controllerHandler' => Controller\TokenController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'meus-veiculos-integ' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/meus-veiculos-integ',
                            'defaults' => [
                                'controllerHandler' => Controller\MeusVeiculosIntegradorController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'motor' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/motor',
                            'defaults' => [
                                'controllerHandler' => Controller\MotorController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'revendas' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/revendas',
                            'defaults' => [
                                'controllerHandler' => Controller\RevendasController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'estados' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/estados',
                            'defaults' => [
                                'controllerHandler' => Controller\EstadosController::class,
                            ],
                            'may_terminate' => false,
                        ],
                    ],
                    'cidades' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/cidades',
                            'defaults' => [
                                'controllerHandler' => Controller\CidadesController::class,
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
            Controller\MeusVeiculosIntegradorController::class => InvokableFactory::class,
            Controller\MotorController::class => InvokableFactory::class,
            Controller\RevendasController::class => InvokableFactory::class,
            Controller\EstadosController::class => InvokableFactory::class,
            Controller\CidadesController::class => InvokableFactory::class,
            Controller\CadastrosController::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            // Middleware
            TokenMiddleware::class => Middleware\Factory\TokenMiddlewareFactory::class,
            Middleware\DispatchMiddleware::class => Middleware\Factory\MiddlewareGenericFactory::class,
        ],
    ],
];
