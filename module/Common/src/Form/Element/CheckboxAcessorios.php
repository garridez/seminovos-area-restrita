<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\MultiCheckbox;

class CheckboxAcessorios extends MultiCheckbox
{

    private $optionsVeiculoTipo = [];

    public function __construct($name = 'acessorios', $options = array())
    {
        $options = array_merge([
            'label' => 'Acessórios',
            'name' => 'acessorios'
            ], $options);

        parent::__construct($name, $options);
    }

    /**
     * Serve para definir os options dentro do select de acordo com o tipo de veículo
     * @param int $tipoVeiculo ID do tipo de veículo
     * @return $this
     */
    public function setVeiculoTipo($tipoVeiculo)
    {
        $this->setValueOptions($this->optionsVeiculoTipo[$tipoVeiculo]);
        return $this;
    }

    public function setOptionsVeiculoTipo($optionsVeiculoTipo)
    {
        $this->optionsVeiculoTipo = $optionsVeiculoTipo;
        return $this;
    }
}
