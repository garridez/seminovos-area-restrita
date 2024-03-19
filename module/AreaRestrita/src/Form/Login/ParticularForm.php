<?php

namespace AreaRestrita\Form\Login;

use Laminas\Form\Element;
use Laminas\Form\Factory as FormFactory;
use Laminas\Form\Form;

class ParticularForm extends Form
{
    public function __construct($name = 'login-particular-form', $options = [])
    {
        parent::__construct($name, $options);

        (new FormFactory())->configureForm(
            $this,
            [
                'elements' => [
                    [
                        'spec' => [
                            'type' => Element\Hidden::class,
                            'name' => 'type',
                            'attributes' => [
                                'value' => $name,
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Hidden::class,
                            'name' => 'tipoCadastro',
                            'attributes' => [
                                'value' => 2,
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Email::class,
                            'name' => 'usuarioEmail',
                            'options' => [
                                'label' => 'E-mail',
                            ],
                            'attributes' => [
                                'required' => true,
                                'minlength' => 5,
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => Element\Password::class,
                            'name' => 'usuarioSenha',
                            'options' => [
                                'label' => 'Senha',
                            ],
                            'attributes' => [
                                'required' => true,
                                'minlength' => 4,
                            ],
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
                            ['name' => 'notempty'],
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
