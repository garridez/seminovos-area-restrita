<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectCombustivel extends Select
{

    public function __construct($name = 'idCombustivel', $options = array())
    {
        $options = array_merge([
            'label' => 'Combustível',
        ], $options);

        parent::__construct($name, $options);
    }

    /**
     *
     * Seta os tipos de combustiveis de com o tipo de veiculo
     *
     * @param int $$tipoVeiculo
     */
    public function setCombustivelFromVeiculo($tipoVeiculo)
    {
        $valueOptions = null;
        switch ($tipoVeiculo) {
            case 1:
                $valueOptions = [
                    '' => 'Selecione',
                    1 => 'Álcool',
                    9 => 'Álcool + Kit Gás',
                    2 => 'Bi-Combustível',
                    10 => 'Bi-Combustível + Kit Gás',
                    3 => 'Diesel',
                    11 => 'Elétrico',
                    4 => 'Gasolina',
                    5 => 'Gasolina + Kit Gás',
                    8 => 'Híbrido(combustão +  eletrico)',
                    6 => 'Kit Gás',
                    7 => 'Tetra Fuel',
                ];
                break;
                case 2:
                $valueOptions = [
                    '' => 'Selecione',
                    3 => 'Diesel',
                ];
                break;
            case 3:
                $valueOptions = [
                    '' => 'Selecione',
                    2 => 'Bi-Combustível',
                    11 => 'Elétrico',
                    4 => 'Gasolina',
                ];
                break;
        }

        $this->setValueOptions($valueOptions);
        return $this;
    }
}
