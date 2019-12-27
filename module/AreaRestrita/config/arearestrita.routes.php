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
            'guia' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/guia',
                    'defaults' => [
                        'action' => 'guia',
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
    'historico-pagamentos' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'historico-pagamentos',
            'defaults' => [
                'controller' => Ctrl\HistoricoPagamentosController::class,
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
    'meus-veiculos' => [
        'type' => Http\Literal::class,
        'options' => [
            'route' => 'meus-veiculos',
            'defaults' => [
                'controller' => Ctrl\MeusVeiculosController::class,
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
            'pesquisa' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/pesquisa/:idVeiculo',
                    'defaults' => [
                        'action' => 'pesquisa',
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
                        'action' => 'reativar',
                    ],
                ],
            ],
            'ativar' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/ativar/:idVeiculo',
                    'defaults' => [
                        'action' => 'reativar',
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
            'inativar' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/inativar/:idVeiculo',
                    'defaults' => [
                        'action' => 'inativar',
                    ],
                ],
            ],
            'excluir' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/excluir/:idVeiculo',
                    'defaults' => [
                        'action' => 'excluir',
                    ],
                ],
            ],
            'propostas' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/propostas/:idVeiculo',
                    'defaults' => [
                        'action' => 'propostas',
                    ],
                ],
            ],
            'qtd-anuncios-menu' => [
                'type' => Http\Literal::class,
                'options' => [
                    'route' => '/qtd-anuncios-menu',
                    'defaults' => [
                        'action' => 'qtdAnunciosMenu',
                    ],
                ],
            ],
        ],
    ],
    'meu-site' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'meu-site',
            'defaults' => [
                'controller' => Ctrl\MeuSiteController::class,
                'action' => 'index'
            ],
        ],
    ],
    'alterar-senha' => [
        'type' => Http\Segment::class,
        'options' => [
            'route' => 'alterar-senha',
            'defaults' => [
                'controller' => Ctrl\MeusDadosController::class,
                'action' => 'alterar-senha'
            ],
        ],
    ],

    'painel' => [
        'type' => Http\Literal::class,
        'options' => [
            'route' => 'painel',
            'defaults' => [
                'controller' => Ctrl\PainelController::class,
                'action' => 'index'
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'detalhes' => [
                'type' => Http\Segment::class,
                'options' => [
                    'route' => '/:idVeiculo',
                    'constraints' => [
                            'idVeiculo' => '[0-9]+',
                    ],
                    'defaults' => [
                        'action' => 'detalhe-anuncio'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'cliques' => [
                        'type' => Http\Literal::class,
                        'options' => [
                            'route' => '/cliques',
                            'defaults' => [
                                'action' => 'cliques'
                            ],
                        ]
                    ]
                ]

            ],
            'contadorPorMarca' => [
                'type' => Http\Literal::class,
                'options' => [
                    'route' => '/contador-por-marca',
                    'defaults' => [
                        'action' => 'contador-por-marca'
                    ]
                ],
            ],
            'contadorPorModelo' => [
                'type' => Http\Literal::class,
                'options' => [
                    'route' => '/contador-por-modelo',
                    'defaults' => [
                        'action' => 'contador-por-modelo'
                    ]
                ],
            ],
            'contadorPorCategoria' => [
                'type' => Http\Literal::class,
                'options' => [
                    'route' => '/contador-por-categoria',
                    'defaults' => [
                        'action' => 'contador-por-categoria'
                    ]
                ],
            ],
        ],
    ],
];
