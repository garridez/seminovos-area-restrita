<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use Laminas\Form\Element;
use Laminas\Form\Form;

class PrecoForm extends Form
{
    public function __construct($name = 'form_precoVeiculo', $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'observacoes',
            'options' => [
                'label' => 'Observações sobre o veículo',
            ],
            'attributes' => [
                'maxlength' => 650,
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'kilometragem',
            'options' => [
                'label' => 'Kilometragem',
            ],
            'attributes' => [
                'required' => false,
                'data-mask' => '000.000.000.000.000',
                'data-mask-options' => json_encode([
                    'reverse' => true,
                ]),
                'placeholder' => '52.000',
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'valor',
            'options' => [
                'label' => 'Valor :',
            ],
            'attributes' => [
                'required' => true,
                'id' => "valor",
                'data-mask' => '000.000.000.000.000,00',
                'data-mask-options' => json_encode([
                    'reverse' => true,
                ]),
                'placeholder' => 'Ex: 48.000,00',
            ],
        ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'combinarValor',
            'options' => [
                'label' => 'Exibir preço',
                'use_hidden_element' => true,
                'checked_value' => '0',
                'unchecked_value' => '1',
            ],
        ]);

        /**
         * rever quando o Felipe normalizar
         */
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'flag_km',
            'options' => [
                'label' => 'Exibir Km?',
                'use_hidden_element' => true,
                'checked_value' => '0',
                'unchecked_value' => '1',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'financiamento',
            'options' => [
                'label' => 'Possui financiamento',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'flagFinanciar',
            'options' => [
                'label' => 'Este veículo pode ser financiado?',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
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

    public function setIsEdition()
    {
        // $this->get('flagLeilao')
    //     ->setAttribute('readonly', true)
    //     ->setAttribute('disabled', true);
    }
}
