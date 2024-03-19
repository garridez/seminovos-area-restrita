<?php

namespace SnBH\Common\Form\Element\Factory;

use interop\container\containerinterface;
use SnBH\Common\Form\Element\SelectCidades;

class SelectCidadeFactory extends AbstractElementFactory
{
    public function __invoke(containerinterface $container, $requestedName, $options = null)
    {
        $selectCidades = new SelectCidades();

        $options = $this->getOptions($container);

        $selectCidades->setValueOptions($options);

        return $selectCidades;
    }

    protected function getOptions($container)
    {
        $cidades = $this->getApiClient($container)
        ->cidadesGet([], null, 1)
        ->getData();

        $options = [
            '' => 'Selecione',
        ];

        foreach ($cidades as $cidade) {
            $options[$cidade['idCidade']] = $cidade['cidade'];
        }
        return $options;
    }
}
