<?php

namespace AreaRestritaAnuncio\Form\Cadastro;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Validator\ValidatorChain;

class CadastroCarroBolsoForm extends Form
{
    public function __construct($name = 'form_cadastroCarroBolso', $options = [])
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
                'class' => 'form-control',
                'placeholder' => 'Nome Sobrenome'
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
                'class' => 'form-control',
                'placeholder' => 'seuemail@example.com.br',
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
    }
}
