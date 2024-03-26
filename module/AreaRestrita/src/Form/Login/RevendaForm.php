<?php

namespace AreaRestrita\Form\Login;

use Laminas\Form\Element;
use Laminas\Form\Factory as FormFactory;
use Laminas\Form\Form;

class RevendaForm extends Form
{
    /**
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'login-revenda-form', $options = [])
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
                                'value' => 1,
                            ],
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
                                'required' => true,
                                'minlength' => 5,
                                'data-mask' => '00.000.000/0000-00',
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
                ],
            ]
        );
    }
}
