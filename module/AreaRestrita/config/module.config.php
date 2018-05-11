<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita;

use AreaRestrita\Service\AuthenticationServiceFactory;
use Zend\Router\Http\Literal;
use Zend\Authentication\AuthenticationService;
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
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                        'middleware' => [
                            Middleware\LoginMiddleware::class,
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
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\AuthController::class => InvokableFactory::class,
            Controller\RotaExemploController::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => AuthenticationServiceFactory::class,
            Middleware\LoginMiddleware::class => Middleware\Factory\LoginMiddlewareFactory::class,
            Middleware\DispatchMiddleware::class => Middleware\Factory\MiddlewareGenericFactory::class,
            Service\AuthManager::class => Service\AuthManagerFactory::class
        ]
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
    ],
    'SnBH\ApiModel' => [
        'model_factory_namespace_prefix' => [
            Model::class
        ]
    ]
];
