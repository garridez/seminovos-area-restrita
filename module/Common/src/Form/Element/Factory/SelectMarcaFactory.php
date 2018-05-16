<?php

namespace SnBH\Common\Form\Element\Factory;

use SnBH\Common\Form\Element\SelectMarca;
use Interop\Container\ContainerInterface;

class SelectMarcaFactory extends AbstractElementFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
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
                'idTipo' => 1
                ], null, 1)
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
