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
            'name' => 'nome',
            'options' => [
                'label' => 'Nome',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);

        $this->add([
            'type' => Element\Date::class,
            'name' => 'dataNascimento',
            'options' => [
                'label' => 'Data de Nascimento',
            ],
        ]);

        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Email::class,
            'name' => 'email2',
            'options' => [
                'label' => 'Confirmação de E-mail',
            ],
            'attributes' => [
                'required' => true,
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
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'cpf',
            'options' => [
                'label' => 'CPF',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'idEstado',
            'options' => [
                'label' => 'Estado',
                'value_options' => [
                    '' => 'Selecione',
                    '11' => 'MINAS GERAIS',
                    '26' => 'SÃO PAULO',
                    '20' => 'RIO DE JANEIRO',
                    '27' => 'TOCANTINS',
                ],
            ],
            'attributes' => [
                'required' => true,
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
            ]
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_1',
            'options' => [
                'label' => 'Telefone Residencial',
            ],
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_2',
            'options' => [
                'label' => 'Celular',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_2_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
                'checked_value' => 1,
                'unchecked_value' => 0,
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
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_3',
            'options' => [
                'label' => 'Celular',
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
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_3_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
                'checked_value' => 1,
                'unchecked_value' => 0,
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
            ]
        ]);
        $this->add([
            'type' => Element\Password::class,
            'name' => 'senha1',
            'options' => [
                'label' => 'Confirmar Senha',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'termo',
            'options' => [
                'label' => 'Sim',
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Próxima',
            ],
        ]);

        $this->configureInputFilter();
    }

    protected function configureInputFilter()
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'nome',
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
            'name' => 'email2',
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
            'name' => 'cpf',
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
            'name' => 'senha',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'senha1',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'termo',
            'required' => true,
        ]);
    }
}
