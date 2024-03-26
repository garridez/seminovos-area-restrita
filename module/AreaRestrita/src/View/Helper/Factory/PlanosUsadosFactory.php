<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Planos;
use AreaRestrita\View\Helper\ArrayData;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class PlanosUsadosFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var Planos $planos */
        $planos = $container->get(Planos::class);
        $res = $planos->getPlanosUsados();
        $data = [];
        foreach ($res as $row) {
                $data[$row['idPlano']] = $row;
        }
        return new ArrayData($data);
    }
}
