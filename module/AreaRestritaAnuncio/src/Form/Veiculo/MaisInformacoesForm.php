<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use Zend\Form\Form;
use Zend\Form\Element;

class MaisInformacoesForm extends Form
{

    public function __construct($name = 'form_maisInformacoesVeiculo', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'observacoes',
            'options' => [
                'label' => 'Observações sobre o veículo',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Radio::class,
            'name' => 'idTroca',
            'options' => [
                'label' => 'Aceita Troca?',
                'value_options' => [
                    '4' => 'SIM',
                    '1' => 'NÃO',
                ],
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'termo',
            'options' => [
                'label' => 'Li e aceito os termos de responsabilidade e a política de privacidade.',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'attributes' => [
                'value' => 1,
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
            'name' => 'idTroca',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'termo',
            'required' => true,
        ]);
    }
}
