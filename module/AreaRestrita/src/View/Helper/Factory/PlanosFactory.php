<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Planos;
use AreaRestrita\View\Helper\ArrayData;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PlanosFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var Planos $planos */
        $planos = $container->get(Planos::class);
        return new ArrayData($planos->getCurrent());
    }
}
