<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\Select;

class SelectAnoModelo extends Select
{
    public function getAnoDe(): array
    {
        $listaAnos = [];
        $anoMaiorAtual = date('Y') + 1;
        for ($ano = $anoMaiorAtual; $ano >= 1925; $ano--) {
            $listaAnos[(int) $ano] = $ano;
        }

        return $listaAnos;
    }

    public function getAnoAte(): array
    {
        $listaAnos = $this->getAnoDe();
        $proximoAno = date('Y') + 1;

        $mes = (int) date('m');

        if ($mes > 1) {
            return ['' => 'Selecione', $proximoAno => $proximoAno] + $listaAnos;
        }

        return ['' => 'Selecione'] + $listaAnos;
    }

    /** @var array */
    protected $valueOptions = [];

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'anoModelo', $options = [])
    {
        $this->valueOptions = $this->getAnoAte();

        $options = array_merge([
            'label' => 'Ano Modelo',
            'name' => 'anoModelo',
        ], $options);

        parent::__construct($name, $options);
    }
}
