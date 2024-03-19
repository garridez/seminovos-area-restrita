<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\Select;

class SelectAnoFabricacao extends Select
{
    /** @var array */
    protected $valueOptions = [];

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'anoFabricacao', $options = [])
    {
        $this->valueOptions = $this->getAnoDe();
        $options = array_merge([
            'label' => 'Ano Fabricação',
            'name' => 'anoFabricacao',
        ], $options);

        parent::__construct($name, $options);
    }

    public function getAnoDe(): array
    {
        $listaAnos = ['' => 'Selecione'];
        $anoMaiorAtual = date('Y') + 1;
        for ($ano = $anoMaiorAtual; $ano >= 1925; $ano--) {
            $listaAnos[$ano] = $ano;
        }

        return $listaAnos;
    }
}
