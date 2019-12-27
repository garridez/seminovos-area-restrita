<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use Zend\Form\Form;
use Zend\Form\Element;

class PrecoForm extends Form
{

    public function __construct($name = 'form_precoVeiculo', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'valor',
            'options' => [
                'label' => 'Valor :',
            ],
            'attributes' => [
                'required' => true,
                'id' => "valor",
                'data-mask' => '000.000.000.000.000,00' ,
                'data-mask-options' => json_encode([
                    'reverse' => true
                ]),
                'placeholder'=>'Ex: 48.000,00',
            ]
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'combinarValor',
            'options' => [
                'label' => 'Não exibir valor do veículo <b class="text-warning">(não recomendado)</b>',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'financiamento',
            'options' => [
                'label' => 'Possui financiamento',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'flagIpva',
            'options' => [
                'label' => 'IPVA 2020 quitado?',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
        ]);
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Cadastrar',
            ],
        ]);

        $this->configureInputFilter();
    }

    protected function configureInputFilter()
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'valor',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'combinarValor',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'financiamento',
            'required' => true,
        ]);
    }
}