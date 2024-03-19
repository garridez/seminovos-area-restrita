<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\MultiCheckbox;

class CheckboxAcessorios extends MultiCheckbox
{
    /** @var array */
    private $optionsVeiculoTipo = [];

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'acessorios', $options = [])
    {
        $options = array_merge([
            'label' => 'Acessórios',
            'name' => 'acessorios',
        ], $options);

        parent::__construct($name, $options);
    }

    /**
     * Serve para definir os options dentro do select de acordo com o tipo de veículo
     *
     * @param int $tipoVeiculo ID do tipo de veículo
     * @return $this
     */
    public function setVeiculoTipo($tipoVeiculo)
    {
        $this->setValueOptions($this->optionsVeiculoTipo[$tipoVeiculo]);
        return $this;
    }

    /**
     * @param array $optionsVeiculoTipo
     * @return $this
     */
    public function setOptionsVeiculoTipo($optionsVeiculoTipo)
    {
        $this->optionsVeiculoTipo = $optionsVeiculoTipo;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        /**
         * Se estiver no formato da API, converte para o formato do ZF
         */
        if (is_array(current($value))) {
            $value = array_column($value, 'idAcessorio');
        }
        parent::setValue($value);
        return $this;
    }
}
