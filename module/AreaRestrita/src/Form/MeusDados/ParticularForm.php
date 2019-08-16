<?php

namespace AreaRestrita\Form\MeusDados;

use Zend\Form\Form;
use Zend\Form\Element;

class ParticularForm extends Form
{

    public function __construct($name = 'form_particularSite', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'responsavelNome',
            'options' => [
                'label' => 'Nome',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control'
            ]
        ]);

        $this->add([
            'type' => Element\Date::class,
            'name' => 'dataNascimento',
            'options' => [
                'label' => 'Data de Nascimento'
            ],
            'attributes' => [
                'class' => 'form-control'
            ]
        ]);

        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control'
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'rg',
            'options' => [
                'label' => 'Rg',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control'
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'cpfResponsavel',
            'options' => [
                'label' => 'CPF',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control',
                'data-mask' => '000.000.000-00',
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'idEstado',
            'options' => [
                'label' => 'Estado',
                'value_options' => [
                    '' => 'Selecione',
                    '1' => 'Acre',
                    '2' => 'Alagoas',
                    '3' => 'Amazonas',
                    '4' => 'Amapá',
                    '5' => 'Bahia',
                    '6' => 'Ceará',
                    '7' => 'Distrito Federal',
                    '8' => 'Espírito Santo',
                    '9' => 'Goiás',
                    '10' => 'Maranhão',
                    '11' => 'Minas Gerais',
                    '12' => 'Mato Grosso do Sul',
                    '13' => 'Mato Grosso',
                    '14' => 'Pará',
                    '15' => 'Paraíba',
                    '16' => 'Pernambuco',
                    '17' => 'Piauí',
                    '18' => 'Paraná',
                    '19' => 'Rio de Janeiro',
                    '20' => 'Rio Grande do Norte',
                    '21' => 'Rondônia',
                    '22' => 'Roraima',
                    '23' => 'Rio Grande do Sul',
                    '24' => 'Santa Catarina',
                    '25' => 'Sergipe',
                    '26' => 'São Paulo',
                    '27' => 'Tocantins',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'value' => 11
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'idCidade',
            'options' => [
                'label' => 'Cidade',
                'value_options' => [
                    '' => 'Selecione',
                    '2700' => 'BH',
                    '2922' => 'CONTAGEM',
                    '2' => 'BETIM',
                    '3' => 'NOVA LIMA',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control'
            ]
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_1',
            'options' => [
                'label' => 'Telefone Residencial',
            ],
            'attributes' => [
                'class' => 'form-control',
                'data-mask' => '(00) 0000-0000',
                'placeholder' => '(__) ____-____',
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_2',
            'options' => [
                'label' => 'Celular',
            ],
            'attributes' => [
                'class' => 'form-control',
                'data-mask' => '(00) 90000-0000',
                'placeholder' => '(__) _____-____',
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
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_3',
            'options' => [
                'label' => 'Celular',
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
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_3_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
//                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
        ]);
        $this->add([
            'type' => Element\Password::class,
            'name' => 'senha',
            'options' => [
                'label' => 'Nova Senha',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control'
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
                'class' => 'form-control'
            ]
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
        $inputFilter->add([
            'name' => 'rg',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StringToUpper'],
            ],
//            'validators' => [
//                [
//                    'name' => \Zend\Validator\Regex::class,
//                    'options' => [
//                        'pattern' => '/^[A-Z]{2}\s[0-9]{2,3}\.[0-9]{3}\.[0-9]{3}$|^[A-Z][0-9]{6}-[0-9]$/'
//                    ]
//                ],
//            ],
        ]);
        $inputFilter->add([
            'name' => 'cpfResponsavel',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'idEstado',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'idCidade',
            'required' => true,
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
        $inputFilter->add([
            'name' => 'operadora_1',
            'required' => false,
        ]);
        #campo não obrigatório, porém sem o required igual a false não funciona
        $inputFilter->add([
            'name' => 'operadora_3',
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
    }
}
