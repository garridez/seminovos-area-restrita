<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio;

use AreaRestrita\Middleware\CheckIdVeiculoMiddleware;
use AreaRestrita\Middleware\DispatchMiddleware;
use AreaRestrita\Middleware\LoginMiddleware;
use AreaRestritaAnuncio\Controller\CadastrarController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'session_containers' => [
        Module::SESSION_NAMESPACE
    ],
    'router' => [
        'routes' => [
            'criar-anuncio-flow' => [
                'may_terminate' => true,
                'type' => Segment::class,
                'options' => [
                    'route' => '/criar-anuncio-flow',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'criar-anuncio-flow'
                    ]
                ],
            ],
            'criar-anuncio' => [
                'may_terminate' => true,
                'type' => Segment::class,
                'options' => [
                    'route' => '/:tipo[/:idVeiculo]',
                    'constraints' => [
                        'tipo' => 'moto|carro|caminhao',
                        'idVeiculo' => '[0-9]{4,}|novo',
                    ],
                    'defaults' => [
                        'controller' => Controller\CriarAnuncioController::class,
                        'action' => 'index',
                        'idVeiculo' => 'novo',
                        'middleware' => [
                            CheckIdVeiculoMiddleware::class,
                            DispatchMiddleware::class,
                        ]
                    ]
                ],
                'child_routes' => [
                    'check-login' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:email',
                            'constraints' => [
                                'email' => '.*@.*',
                            ],
                            'defaults' => [
                                'controller' => Controller\LoginController::class,
                                'action' => 'check-login',
                            ],
                        ]
                    ],
                    'login' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/entrar[/:email]',
                            'constraints' => [
                                'email' => '.*@.*',
                            ],
                            'defaults' => [
                                'controller' => Controller\LoginController::class,
                                'action' => 'login',
                            ],
                        ]
                    ],
                    'criar-cadastro' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/me-cadastrar[/:email]',
                            'constraints' => [
                                'email' => '.*@.*',
                            ],
                            'defaults' => [
                                'controller' => Controller\CadastrarController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'anuncio' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '',
                            'defaults' => [
                            ],
                        ],
                        'child_routes' => [
                            'dados' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/',
                                    'defaults' => [
                                        'controller' => Controller\DadosVeiculoController::class,
                                    ],
                                ],
                                'child_routes' => [
                                    'pages' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => ':action',
                                            'constraints' => [
                                                'action' => 'dados|preco|mais-informacoes|fotos|video'
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                            'planos' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/planos',
                                    'defaults' => [
                                        'controller' => Controller\PlanoController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'pagamento' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/checkout',
                                    'defaults' => [
                                        'controller' => Controller\PagamentoController::class,
                                        'action' => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'metodos' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/:action[/.*]',
                                            'route' => '/:action',
                                            'constraints' => [
                                                'action' => 'concluido|gratis|comprovante|aguardando-pagamento|plano-renovado|processar|cancelar-pagamentos-em-aberto|retorno-cielo|retorno-pagseguro',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'finalizar' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/finalizar',
                                    'defaults' => [
                                        'controller' => Controller\FinalizarController::class,
                                        'action' => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'metodos' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/:action[/.*]',
                                            'route' => '/:action',
                                            'constraints' => [
                                                'action' => 'concluido|gratis|comprovante|aguardando-pagamento|plano-renovado|processar|cancelar-pagamentos-em-aberto|retorno-cielo|retorno-pagseguro',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            
                        ],
                    ],
                ],
            ],
            'criar-cadastro' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/me-cadastrar[/:email]',
                    'constraints' => [
                        'email' => '.*@.*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'remember-pass' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/remember-pass',
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'rememberPass'
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\CadastrarController::class => InvokableFactory::class,
            Controller\CriarAnuncioController::class => InvokableFactory::class,
            Controller\DadosVeiculoController::class => InvokableFactory::class,
            Controller\FinalizarController::class => InvokableFactory::class,
            Controller\IndexController::class => InvokableFactory::class,
            Controller\LoginController::class => InvokableFactory::class,
            Controller\PagamentoController::class => InvokableFactory::class,
            Controller\PlanoController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
