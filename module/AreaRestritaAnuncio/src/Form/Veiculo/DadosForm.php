<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use SnBH\ApiClient\Client as ApiClient;
use SnBH\Common\Form\Element\CheckboxAcessorios;
use SnBH\Common\Form\Element\SelectAnoFabricacao;
use SnBH\Common\Form\Element\SelectAnoModelo;
use SnBH\Common\Form\Element\SelectCombustivel;
use SnBH\Common\Form\Element\SelectCor;
use SnBH\Common\Form\Element\SelectMarca;
use SnBH\Common\Form\Element\SelectModelo;
use SnBH\Common\Form\Element\SelectPortas;
use Zend\Form\Element;
use Zend\Form\Form;

class DadosForm extends Form
{

    public function __construct($name = 'form_dadosVeiculo', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'tipoVeiculo',
        ]);

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

        global $container;
        /**
         * @todo mover isso pra uma factory
         */
        /** @var SelectMarca $selectMarca */
        $selectMarca = $container->get(SelectMarca::class);
        $selectMarca->setAttribute('required', true);

        $this->add($selectMarca);

        $this->add([
            'type' => SelectModelo::class,
            'name' => 'modeloCarro',
            'options' => [
                'label' => 'Modelo',
                'value_options' => [
                    '' => 'Selecione o modelo',
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'versao',
            'options' => [
                'label' => 'Versão',
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
                'data-init-plugin' => 'select2',
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
        global $container;
        /** @var CheckboxAcessorios $checkboxAcessorios */
        $checkboxAcessorios = $container->get(CheckboxAcessorios::class);
        $checkboxAcessorios->setName('checkboxacessorios');
        $this->add($checkboxAcessorios);


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

    /**
     * Seta o tipo de veículo dentro dos inputs relevantes
     * @param int $tipoVeiculo
     */
    public function setTipoVeiculo($tipoVeiculo)
    {
        $this->get('tipoVeiculo')->setValue($tipoVeiculo);
        $this->get('checkboxacessorios')->setVeiculoTipo($tipoVeiculo);
    }

    /**
     * Define como apenas leitura alguns campos
     */
    public function setIsEdition($isEdition)
    {
        $isEdition = (boolean) $isEdition;
        $readonly = [
            'idMarca',
            'placa',
            'modeloCarro',
            'motor',
            'idValvula',
            'versao',
            'anoFabricacao',
            'anoModelo',
            'cor',
            'portas',
            'combustivel',
        ];
        foreach ($readonly as $name) {
            $this->get($name)
                ->setAttribute('readonly', $isEdition)
                ->setAttribute('disabled', $isEdition);
        }
    }

    public function populateValues($data, $onlyBase = false)
    {
        if (isset($data['idMarca']) && $data['idMarca']) {
            $this->get('modeloCarro')->setModelosFromMarca($data['idMarca']);
        }
        if (isset($data['acessorios']) && $data['acessorios']) {
            $data['checkboxacessorios'] = $data['acessorios'];
        }
        parent::populateValues($data, $onlyBase);
    }
}
