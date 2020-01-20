<?php

namespace SnBH\Common\Form\Element;

use Zend\Form\Element\Select;

class SelectAnoModelo extends Select
{
    public function getAnoDe()
    {
        $anoMaiorAtual = date('Y');
        for ($ano = $anoMaiorAtual; $ano >= 1930; $ano--) {
            $listaAnos[$ano] = $ano;
        }

        return $listaAnos;
    }

    public function getAnoAte()
    {
        $listaAnos = $this->getAnoDe();
        $proximoAno = date('Y') + 1;

        $mes = intval(date('m'));

        if($mes > 6) {

            return ['' => 'Selecione', $proximoAno => $proximoAno] + $listaAnos;
        }

        return ['' => 'Selecione'] + $listaAnos;

    }

    protected $valueOptions = [];

    public function __construct($name = 'anoModelo', $options = array())
    {
        $this->valueOptions = $this->getAnoAte();
        
        $options = array_merge([
            'label' => 'Ano Modelo',
            'name' => 'anoModelo'
            ], $options);

        parent::__construct($name, $options);
    }
}
