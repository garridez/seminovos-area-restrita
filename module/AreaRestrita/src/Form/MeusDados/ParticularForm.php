<?php

namespace AreaRestrita\Form\MeusDados;

use Zend\Form\Form;
use Zend\Form\Element;

class ParticularForm extends Form
{

    public function __construct($name = 'meus-dados-form', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'rg',
            'options' => [
                'label' => 'Rg',
            ],
        ]);
        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'idCidade',
            'options' => [
                'label' => 'Cidade',
                'value_options' => [
                    '' => 'Selecione',
                    '2921' => 'Bh',
                    '2922' => 'Contagem',
                    '2' => 'Betim',
                    '3' => 'Nova Lima',
                ],
            ],
        ]);
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Submit',
            ],
        ]);

        $this->configureInputFilter();
    }

    protected function configureInputFilter()
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'rg',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StringToUpper'],
            ],
            'validators' => [
                [
                    'name' => \Zend\Validator\Regex::class,
                    'options' => [
                        'pattern' => '/^[A-Z]{2}\s[0-9]{2,3}\.[0-9]{3}\.[0-9]{3}$|^[A-Z][0-9]{6}-[0-9]$/'
                    ]
                ],
            ],
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
            'name' => 'idCidade',
            'required' => true,
        ]);
    }
}
