<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita;

use AreaRestrita\Service\AuthenticationServiceFactory;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\Service\StorageCacheFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'session_containers' => [
        Module::SESSION_NAMESPACE
    ],
    'router' => [
        'routes' => [
            'restrito' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\MeusVeiculosController::class,
                        'action' => 'index',
                        'middleware' => [
                            Middleware\LoginMiddleware::class,
                            Middleware\CheckIdVeiculoMiddleware::class,
                            Middleware\DispatchMiddleware::class,
                        ]
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => require __DIR__ . '/arearestrita.routes.php',
            ],
            'auth' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/entrar',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login-automatico' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/key/:dados',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'login-automatico',
                            ],
                        ],
                    ],
                ]
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/sair',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'logout',
                    ],
                ],
            ],
            'termos' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/termos/[:action]',
                    'defaults' => [
                        'controller' => Controller\TermosController::class,
                    ],
                ],
            ],
            'filtros' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/filtros',
                    'defaults' => [
                        'controller' => Controller\FiltrosController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'check' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/check',
                    'defaults' => [
                        'controller' => Controller\CheckController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'json' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/json',
                    'defaults' => [
                        'controller' => Controller\JsonController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'cidades' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/cidades.json',
                            'defaults' => [
                                'action' => 'cidades',
                            ],
                        ],
                    ]
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => InvokableFactory::class,
            Controller\CheckController::class => InvokableFactory::class,
            Controller\ContratoRevendaController::class => InvokableFactory::class,
            Controller\FaturaController::class => InvokableFactory::class,
            Controller\FiltrosController::class => InvokableFactory::class,
            Controller\FinanceiroController::class => InvokableFactory::class,
            Controller\HistoricoPagamentosController::class => InvokableFactory::class,
            Controller\IndexController::class => InvokableFactory::class,
            Controller\JsonController::class => InvokableFactory::class,
            Controller\MeuSiteController::class => InvokableFactory::class,
            Controller\MeusDadosController::class => InvokableFactory::class,
            Controller\MeusVeiculosController::class => InvokableFactory::class,
            Controller\RotaExemploController::class => InvokableFactory::class,
            Controller\TermosController::class => InvokableFactory::class,
            Controller\PainelController::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'cache' => StorageCacheFactory::class,
            // Auth
            AuthenticationService::class => AuthenticationServiceFactory::class,
            Service\AuthManager::class => Service\AuthManagerFactory::class,
            Service\Identity::class => Service\Factory\IdentityFactory::class,
            // Middleware
            Middleware\LoginMiddleware::class => Middleware\Factory\LoginMiddlewareFactory::class,
            Middleware\DispatchMiddleware::class => Middleware\Factory\MiddlewareGenericFactory::class,
            Middleware\CheckIdVeiculoMiddleware::class => Middleware\Factory\CheckIdVeiculoMiddlewareFactory::class,
        ]
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\UserInfo::class => View\Helper\Factory\UserInfoFactory::class,
            View\Helper\BodyClass::class => View\Helper\Factory\BodyClassFactory::class,
            View\Helper\QtdAnuncios::class => View\Helper\Factory\QtdAnunciosFactory::class,
            View\Helper\ExpiracaoRevenda::class => View\Helper\Factory\ExpiracaoRevendaFactory::class,
            Form\View\Helper\FormCheckbox::class => InvokableFactory::class,
            Form\View\Helper\FormMultiCheckbox::class => InvokableFactory::class
        ],
        'aliases' => [
            'userInfo' => View\Helper\UserInfo::class,
            'bodyClass' => View\Helper\BodyClass::class,
            'qtdAnuncios' => View\Helper\QtdAnuncios::class,
            'expiracaoRevenda' => View\Helper\ExpiracaoRevenda::class,
            'formcheckbox' => Form\View\Helper\FormCheckbox::class,
            'form_checkbox' => Form\View\Helper\FormCheckbox::class,
            'formCheckbox' => Form\View\Helper\FormCheckbox::class,
            'FormCheckbox' => Form\View\Helper\FormCheckbox::class,
            'formmulticheckbox' => Form\View\Helper\FormMultiCheckbox::class,
            'form_multicheckbox' => Form\View\Helper\FormMultiCheckbox::class,
            'formMultiCheckbox' => Form\View\Helper\FormMultiCheckbox::class,
            'FormMultiCheckbox' => Form\View\Helper\FormMultiCheckbox::class,
            'formradio' => Form\View\Helper\FormRadio::class,
            'form_radio' => Form\View\Helper\FormRadio::class,
            'formRadio' => Form\View\Helper\FormRadio::class,
            'FormRadio' => Form\View\Helper\FormRadio::class,
        ],
        'invokables' => [
            'formCheckbox' => Form\View\Helper\FormCheckbox::class,
            'formcheckbox' => Form\View\Helper\FormCheckbox::class,
            'formMultiCheckbox' => Form\View\Helper\FormMultiCheckbox::class,
            'formmulticheckbox' => Form\View\Helper\FormMultiCheckbox::class,
            'formRadio' => Form\View\Helper\FormRadio::class,
            'formradio' => Form\View\Helper\FormRadio::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'SnBH\ApiModel' => [
        'model_factory_namespace_prefix' => [
            Model::class
        ]
    ]
];
