<?php

namespace AreaRestrita\Form\Login;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Factory as FormFactory;

class RevendaForm extends Form
{

    public function __construct($name = 'login-revenda-form', $options = array())
    {
        parent::__construct($name, $options);

        (new FormFactory)->configureForm($this,
            [
                'elements' => [
                    [
                        'spec' => [
                            'type' => Element\Hidden::class,
                            'name' => 'type',
                            'attributes' => [
                                'value' => $name,
                            ]
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Hidden::class,
                            'name' => 'tipoCadastro',
                            'attributes' => [
                                'value' => 1,
                            ]
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Text::class,
                            'name' => 'usuarioEmail',
                            'options' => [
                                'label' => 'CNPJ',
                            ],
                            'attributes' => [
                                'data-mask' => '00.000.000/0000-00',
                            ]
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Password::class,
                            'name' => 'usuarioSenha',
                            'options' => [
                                'label' => 'Senha',
                            ]
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Submit::class,
                            'name' => 'entrar',
                            'attributes' => [
                                'value' => 'Entrar',
                            ],
                        ],
                    ],
                ],
                'input_filter' => [
                    'usuarioSenha' => [
                        'required' => true,
                        'validators' => [
                            ['name' => 'notempty',],
                        ],
                    ],
                ],
            ]
        );
    }
}
