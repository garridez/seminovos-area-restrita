<?php

namespace SnBH\Common\Form\Element\Factory;

use interop\container\containerinterface;
use SnBH\Common\Form\Element\SelectMarca;

class SelectMarcaFactory extends AbstractElementFactory
{
    public function __invoke(containerinterface $container, $requestedName, $options = null)
    {
        $selectCidades = new SelectMarca();

        $options = $this->getOptions($container);

        $selectCidades->setValueOptions($options);

        return $selectCidades;
    }

    protected function getOptions($container)
    {
        $marcas = $this->getApiClient($container)
        ->marcasGet([
            'idTipo' => 1,
        ], null, true)
        ->getData();

        $options = [
            '' => 'Selecione',
        ];
        foreach ($marcas as $marca) {
            $options[$marca['idMarca']] = $marca['marca'];
        }

        return $options;
    }
}
