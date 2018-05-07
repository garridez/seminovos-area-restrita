<?php

namespace AreaRestrita\Form\Login;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Factory as FormFactory;

class ParticularForm extends Form
{

    public function __construct($name = 'login-particular-form', $options = array())
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
                                'value' => 2,
                            ]
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Email::class,
                            'name' => 'usuarioEmail',
                            'options' => [
                                'label' => 'Email',
                            ],
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
                    'usuarioEmail' => [
                        'required' => true,
                        'filters' => [
                            ['name' => 'StringTrim'],
                            ['name' => 'StringToLower'],
                        ],
                    ],
                ],
            ]
        );
    }
}
