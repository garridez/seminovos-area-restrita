<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio;


use AreaRestritaAnuncio\Controller\CadastrarController;
use AreaRestrita\Controller\Factory\AbstractFactory;
use AreaRestrita\Controller\Factory\AbstractFactoryClient;
use AreaRestrita\Middleware\CheckIdVeiculoMiddleware;
use AreaRestrita\Middleware\DispatchMiddleware;
use AreaRestrita\Middleware\LoginMiddleware;
use AreaRestrita\Middleware;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

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
            'selecionar-tipo' => [
                'may_terminate' => true,
                'type' => Segment::class,
                'options' => [
                    'route' => '/criar-anuncio',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'selecionar-tipo'
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
                    'email-disponivel' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/email-disponivel/:email',
                            'defaults' => [
                                'controller' => Controller\CadastrarController::class,
                                'action' => 'email-disponivel',
                            ],
                        ],
                    ],
                    'cpf-disponivel' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/cpf-disponivel/:cpf',
                            'defaults' => [
                                'controller' => Controller\CadastrarController::class,
                                'action' => 'cpf-disponivel',
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
                                                'action' => 'dados|preco|mais-informacoes|fotos|video|opcionais'
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
                            'servicos-adicionais' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/servicos-adicionais',
                                    'defaults' => [
                                        'controller' => Controller\ServicosAdicionaisController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'placa-disponivel' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/placa-disponivel/:placa',
                                    'defaults' => [
                                        'controller' => Controller\DadosVeiculoController::class,
                                        'action' => 'placa-disponivel',
                                        'middleware' => [
                                            Middleware\LoginMiddleware::class,
                                            Middleware\CheckIdVeiculoMiddleware::class,
                                            Middleware\DispatchMiddleware::class,
                                        ]
                                    ],
                                ],
                            ],
                            'versao' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/versao',
                                    'defaults' => [
                                        'controller' => Controller\DadosVeiculoController::class,
                                        'action' => 'getVersao',
                                    ],
                                ],
                            ],
                            'gratis' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/gratis',
                                    'defaults' => [
                                        'controller' => Controller\DadosVeiculoController::class,
                                        'action' => 'gratis',
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
                                                'action' => 'concluido|gratis|comprovante|aguardando-pagamento|plano-renovado|processar|cancelar-pagamentos-em-aberto|retorno-cielo|retorno-pagseguro|pagamento-pix',
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
                                                'action' => 'concluido|gratis|comprovante|aguardando-pagamento|plano-renovado|processar|cancelar-pagamentos-em-aberto|retorno-cielo|retorno-pagseguro|pagamento-pix',
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
            'cadastro-simples' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/cadastro-simples',
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'cadastro-simples',
                    ],
                ],
            ],
            'carro-bolso' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/carro-bolso',
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'carro-bolso',
                    ],
                ],
            ],
            'remember-pass-phone' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/remember-pass-phone',
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'rememberPassPhone'
                    ],
                ],
            ],
            'validate-token' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/validate-token',
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'validateToken'
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
            'email-telefone-from-cpf-cnpj' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/email-telefone-from-cpf-cnpj',
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'getEmailTelefoneFromCpfOuCnpj'
                    ],
                ],
            ],
            'remember-pass-save' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/remember-pass-save',
                    'defaults' => [
                        'controller' => Controller\CadastrarController::class,
                        'action' => 'rememberPassSave'
                    ],
                ],
            ],
            'clear-cache' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/clear-cache[/:idVeiculo]',
                    'defaults' => [
                        'controller' => Controller\DadosVeiculoController::class,
                        'action' => 'clear-cache'
                    ],
                ],
            ],
            'chat-criar' => [
                //'may_terminate' => true,
                'type' => Literal::class,
                'options' => [
                    'route' => '/criar-anuncio-v2',
                    'defaults' => [
                        'controller' => Controller\ChatCriarAnuncioController::class,
                        'action' => 'index'
                    ]
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
            Controller\ServicosAdicionaisController::class => InvokableFactory::class,
            Controller\ChatCriarAnuncioController::class => InvokableFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
