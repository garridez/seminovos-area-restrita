<?php

namespace AreaRestrita\Form\MeusDados;

use Zend\Form\Form;
use Zend\Form\Element;

class RevendaForm extends Form
{

    public function __construct($name = 'form_revendaSite', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'nomeFantasia',
            'options' => [
                'label' => 'Nome Fantasia',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'responsavelNome',
            'options' => [
                'label' => 'Nome Responsável',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_1',
            'options' => [
                'label' => 'Telefone 1',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'data-mask' => '(00) 90000-0000',
                'placeholder' => '(__) _____-____',
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'operadora_1',
            'options' => [
                'label' => 'Operadora 1',
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
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_1_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'value' => 1,
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_2',
            'options' => [
                'label' => 'Telefone 2',
            ],
            'attributes' => [
                'class' => 'form-control',
                'data-mask' => '(00) 90000-0000',
                'placeholder' => '(__) _____-____',
            ],
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'operadora_2',
            'options' => [
                'label' => 'Operadora 2',
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
                'value' => 1,
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_3',
            'options' => [
                'label' => 'Telefone 3',
            ],
            'attributes' => [
                'class' => 'form-control',
                'data-mask' => '(00) 90000-0000',
                'placeholder' => '(__) _____-____',
            ],
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'operadora_3',
            'options' => [
                'label' => 'Operadora 3',
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
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_3_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'value' => 1,
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_4',
            'options' => [
                'label' => 'Telefone 4',
            ],
            'attributes' => [
                'class' => 'form-control',
                'data-mask' => '(00) 90000-0000',
                'placeholder' => '(__) _____-____',
            ],
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'operadora_4',
            'options' => [
                'label' => 'Operadora 4',
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
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_4_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'value' => 1,
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'celular_financeiro',
            'options' => [
                'label' => 'Celular Financeiro',
            ],
            'attributes' => [
                'class' => 'form-control',
                'data-mask' => '(00) 90000-0000',
                'placeholder' => '(__) _____-____',
            ],
        ]);
        $this->add([
            'type' => Element\Email::class,
            'name' => 'email_financeiro',
            'options' => [
                'label' => 'E-mail Financeiro',
            ],
            'attributes' => [
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'E-mail Principal',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Email::class,
            'name' => 'email_secundario',
            'options' => [
                'label' => 'E-mail Secundário',
            ],
            'attributes' => [
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'facebook',
            'options' => [
                'label' => 'Facebook',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder'=>'https://www.facebook.com/{pagina}',
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'instagram',
            'options' => [
                'label' => 'Instagram',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder'=>'https://www.instagram.com/{usuario}',
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'youtube',
            'options' => [
                'label' => 'Youtube',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder'=>'https://www.youtube.com/user/{canal}',
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'twitter',
            'options' => [
                'label' => 'Twitter',
            ],
            'attributes' => [
                'class' => 'form-control',
                'placeholder'=>'https://twitter.com/{usuario}',
            ],
        ]);
        $this->add([
            'type' => Element\Password::class,
            'name' => 'senha',
            'options' => [
                'label' => 'Senha',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Password::class,
            'name' => 'confirmacaoSenha',
            'options' => [
                'label' => 'Confirmar Senha',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Salvar',
                'class' => 'btn btn-success btn-cons',
            ],
        ]);

        $this->configureInputFilter();
    }

    protected function configureInputFilter()
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'nomeFantasia',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'responsavelNome',
            'required' => true,
        ]);
        #campo não obrigatório, porém sem o required igual a false não funciona
        $inputFilter->add([
            'name' => 'dataNascimento',
            'required' => false,
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
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);
        #campo não obrigatório, porém na função put se o campo estiver vazio não funciona
        $inputFilter->add([
            'name' => 'telefone_1',
            'required' => true,
        ]);
        #campo não obrigatório, porém sem o required igual a false não funciona
        $inputFilter->add([
            'name' => 'operadora_2',
            'required' => false,
        ]);
        #campo não obrigatório, porém sem o required igual a false não funciona
        $inputFilter->add([
            'name' => 'operadora_3',
            'required' => false,
        ]);
        $inputFilter->add([
            'name' => 'operadora_1',
            'required' => false,
        ]);
        $inputFilter->add([
            'name' => 'senha',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'confirmacaoSenha',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'operadora_4',
            'required' => false,
        ]);
        $inputFilter->add([
            'name' => 'email_secundario',
            'required' => false,
        ]);
    }
}
