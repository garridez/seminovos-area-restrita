<?php

namespace AreaRestritaAnuncio\Form\Cadastro;

use Laminas\Form\Form;
use Laminas\Form\Element;

class CadastroSimplesForm extends Form
{

    public function __construct($name = 'form_cadastroSimples', $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => 'seuemail@example.com.br',
            ]
        ]);
        $this->add([
            'type' => Element\Email::class,
            'name' => 'confirmacaoEmail',
            'options' => [
                'label' => 'Confirme seu E-mail',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => 'seuemail@example.com.br',
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'cpfResponsavel',
            'options' => [
                'label' => 'CPF',
            ],
            'attributes' => [
                'required' => false,
                'class' => 'form-control',
                'placeholder' => '000.000.000-00',
                'data-mask' => '000.000.000-00',
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_2',
            'options' => [
                'label' => 'Celular',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'data-mask' => '(00) 00000-0000',
                'placeholder' => '(__) _____-____',
            ],
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'operadora_2',
            'options' => [
                'label' => 'Operadora',
                'value_options' => [
                    '' => 'Selecione',
                    '1' => 'OI',
                    '2' => 'TIM',
                    '3' => 'CLARO',
                    '4' => 'VIVO',
                    '5' => 'NEXTEL',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_2_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'value' => 0,
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'responsavelNome',
            'options' => [
                'label' => 'Nome',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => 'Nome Sobrenome',
                'autocomplete' => 'name',
                'autocapitalize' => 'sentences',
            ]
        ]);
        $this->add([
            'type' => Element\Password::class,
            'name' => 'senha',
            'options' => [
                'label' => 'Senha',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => '******',
                'autocomplete' => 'off',
            ]
        ]);
        $this->add([
            'type' => Element\Password::class,
            'name' => 'confirmacaoSenha',
            'options' => [
                'label' => 'Confirmar Senha',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => '******',
                'autocomplete' => 'off',
            ]
        ]);
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Cadastrar',
                'class' => 'btn btn-laranja btn-block',
            ],
        ]);

        $this->configureInputFilter();
    }

    protected function configureInputFilter()
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'responsavelNome',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StringToLower'],
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => \Laminas\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name' => 'cpfResponsavel',
            'required' => false,
        ]);
        #campo não obrigatório, porém na função put se o campo estiver vazio não funciona
        $inputFilter->add([
            'name' => 'telefone_2',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'senha',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'confirmacaoSenha',
            'required' => true,
        ]);
    }
}