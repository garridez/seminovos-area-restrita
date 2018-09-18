<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use SnBH\Common\Form\Element\CheckboxAcessorios;
use SnBH\Common\Form\Element\SelectAnoFabricacao;
use SnBH\Common\Form\Element\SelectAnoModelo;
use SnBH\Common\Form\Element\SelectCombustivel;
use SnBH\Common\Form\Element\SelectCor;
use SnBH\Common\Form\Element\SelectPortas;
use Zend\Form\Form;
use Zend\Form\Element;

class DadosForm extends Form
{

    public function __construct($name = 'form_dadosVeiculo', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'placa',
            'options' => [
                'label' => 'Placa',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'idMarca',
            'options' => [
                'label' => 'Marca',
                'value_options' => [
                    '' => 'Selecione',
                    '7' => 'Chevrolet',
                    '18' => 'Fiat',
                    '19' => 'Ford',
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'modeloCarro',
            'options' => [
                'label' => 'Modelo',
                'value_options' => [
                    '' => 'Selecione',
                    '232' => 'Chevrolet Zafira',
                    '1964' => 'Fiat Toro',
                    '146' => 'Ford Ranger Cab. Dupla',
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'versao',
            'options' => [
                'label' => 'Versão',
                'value_options' => [
                    '' => 'Selecione',
                    '1' => 'ELX',
                    '2' => 'FIRE',
                    '3' => 'FLEX',
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'motor',
            'options' => [
                'label' => 'Motor',
                'value_options' => [
                    '' => 'Selecione',
                    '1' => '1.0',
                    '2' => '1.6',
                    '3' => '1.2',
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'idValvula',
            'options' => [
                'label' => 'Válvula',
                'value_options' => [
                    '' => 'Selecione',
                    '1' => '16',
                    '2' => '20',
                    '3' => '8',
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => SelectAnoFabricacao::class,
            'name' => 'anoFabricacao',
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => SelectAnoModelo::class,
            'name' => 'anoModelo',
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => SelectPortas::class,
            'name' => 'portas',
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => SelectCor::class,
            'name' => 'cor',
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => SelectCombustivel::class,
            'name' => 'combustivel',
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'kilometragem',
            'options' => [
                'label' => 'Kilometragem',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => CheckboxAcessorios::class,
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
            'name' => 'idMarca',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'modeloCarro',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'versao',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'motor',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'idValvula',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'anoFabricacao',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'anoModelo',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'portas',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'cor',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'combustivel',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'checkboxacessorios',
            'required' => false,
        ]);
        $inputFilter->add([
            'name' => 'selectcor',
            'required' => false,
        ]);
    }
}
