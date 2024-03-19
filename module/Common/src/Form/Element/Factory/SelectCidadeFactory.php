<?php

namespace SnBH\Common\Form\Element\Factory;

use Psr\Container\ContainerInterface;
use SnBH\Common\Form\Element\SelectCidades;

class SelectCidadeFactory extends AbstractElementFactory
{
    /**
     * @param string             $requestedName
     * @param array|null         $options
     * @return SelectCidades
     */
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $selectCidades = new SelectCidades();

        $options = $this->getOptions($container);

        $selectCidades->setValueOptions($options);

        return $selectCidades;
    }

    protected function getOptions(ContainerInterface $container): array
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
