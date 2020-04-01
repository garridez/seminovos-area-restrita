<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SnBH\Integrador;

use Zend\Router\Http\Literal;
use Zend\ServiceManager\Factory\InvokableFactory;

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
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'veiculo' => [
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
                ]
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\VeiculoController::class => InvokableFactory::class,
            Controller\VeiculoFotosController::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => []
    ],
];
