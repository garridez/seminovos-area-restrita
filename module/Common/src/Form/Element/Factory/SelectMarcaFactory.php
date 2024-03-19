<?php

namespace SnBH\Common\Form\Element\Factory;

use Psr\Container\ContainerInterface;
use SnBH\Common\Form\Element\SelectMarca;

class SelectMarcaFactory extends AbstractElementFactory
{
    /**
     * @param string             $requestedName
     * @param array|null         $options
     * @return SelectMarca
     */
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $selectCidades = new SelectMarca();

        $options = $this->getOptions($container);

        $selectCidades->setValueOptions($options);

        return $selectCidades;
    }

    protected function getOptions(ContainerInterface $container): array
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
