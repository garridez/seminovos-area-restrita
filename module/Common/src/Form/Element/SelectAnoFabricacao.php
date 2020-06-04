<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectAnoFabricacao extends Select
{

    public function getAnoDe()
    {
        $listaAnos = array(
            '' => 'Selecione'
        );
        $anoMaiorAtual = date('Y');
        for ($ano = $anoMaiorAtual; $ano >= 1925; $ano--) {
            $listaAnos[$ano] = $ano;
        }

        return $listaAnos;
    }

    protected $valueOptions = [];

    public function __construct($name = 'anoFabricacao', $options = array())
    {
        $this->valueOptions = $this->getAnoDe();
        $options = array_merge([
            'label' => 'Ano Fabricação',
            'name' => 'anoFabricacao'
            ], $options);

        parent::__construct($name, $options);
    }
}
