<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Planos;
use AreaRestrita\View\Helper\ArrayData;
use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PlanosUsadosFactory implements FactoryInterface
{
    public function __invoke(containerinterface $container, $requestedName, ?array $options = null)
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
