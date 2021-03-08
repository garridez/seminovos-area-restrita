<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\View\Helper\ArrayData;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use AreaRestrita\Model\Planos;

class PlanosUsadosFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var Planos $planos */
        $planos = $container->get(Planos::class);
        $res = $planos->getPlanosUsados();
        $data = [];
        foreach($res as $row){
                $data[$row['idPlano']] = $row;
        }
        return new ArrayData($data);
    }
}
