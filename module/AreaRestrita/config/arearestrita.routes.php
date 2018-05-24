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
            'todas-rotas' => [
                'type' => Http\Literal::class,
                'options' => [
                    'route' => '/todas-rotas',
                    'defaults' => [
                        'action' => 'todas-rotas',
                    ],
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
    'meus-dados-particular' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'meus-dados-particular',
            'defaults' => [
                'controller' => Ctrl\MeusDadosParticularController::class,
                'action' => 'index'
            ],
        ],
    ],
    'meus-dados-revenda' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'meus-dados-revenda',
            'defaults' => [
                'controller' => Ctrl\MeusDadosRevendaController::class,
                'action' => 'index'
            ],
        ],
    ],
    'contrato-revenda' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'contrato-revenda',
            'defaults' => [
                'controller' => Ctrl\ContratoRevendaController::class,
                'action' => 'index'
            ],
        ],
    ],
    'historico-pagamentos-particular' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'historico-pagamentos-particular',
            'defaults' => [
                'controller' => Ctrl\HistoricoPagamentosParticularController::class,
                'action' => 'index'
            ],
        ],
    ],
    'historico-pagamentos-revenda' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'historico-pagamentos-revenda',
            'defaults' => [
                'controller' => Ctrl\HistoricoPagamentosRevendaController::class,
                'action' => 'index'
            ],
        ],
    ],
    'fatura-particular' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'fatura/particular/id/:idPagamento',
            'defaults' => [
                'controller' => Ctrl\FaturaController::class,
                'action' => 'particular'
            ],
        ],
    ],
    'fatura-revenda' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'fatura/revenda/id/:idPagamento',
            'defaults' => [
                'controller' => Ctrl\FaturaController::class,
                'action' => 'revenda'
            ],
        ],
    ],
    'financeiro' => [
        'type' => Http\Literal::class,
        'options' => [
            'route' => 'financeiro',
            'defaults' => [
                'controller' => Ctrl\FinanceiroController::class,
                'action' => 'index'
            ],
        ],
    ],
    'meus-veiculos-particular' => [
        'type' => Http\Literal::class,
        'options' => [
            'route' => 'meus-veiculos-particular',
            'defaults' => [
                'controller' => Ctrl\MeusVeiculosParticularController::class,
                'action' => 'index'
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'excluir' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/excluir/:idVeiculo',
                    'defaults' => [
                        'action' => 'excluir',
                    ],
                ],
            ],
            'vendido' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/vendido/:idVeiculo',
                    'defaults' => [
                        'action' => 'vendido',
                    ],
                ],
            ],
            'reativar' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/reativar/:idVeiculo',
                    'defaults' => [
                        'action' => 'reativar',
                    ],
                ],
            ],
            'renovar' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/renovar/:idVeiculo',
                    'defaults' => [
                        'action' => 'renovar',
                    ],
                ],
            ],
            'veiculo' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/:idVeiculo',
                    'defaults' => [
                        'action' => 'veiculo',
                    ],
                ],
            ],
        ],
    ],
    'meus-veiculos-revenda' => [
        'type' => Http\Literal::class,
        'options' => [
            'route' => 'meus-veiculos-revenda',
            'defaults' => [
                'controller' => Ctrl\MeusVeiculosRevendaController::class,
                'action' => 'index'
            ],
        ],
    ],
];
