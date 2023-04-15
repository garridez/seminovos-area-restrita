<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\View\Helper\ArrayData;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use AreaRestrita\Model\Planos;

class PlanosFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var Planos $planos */
        $planos = $container->get(Planos::class);
        return new ArrayData($planos->getCurrent());
    }
}
