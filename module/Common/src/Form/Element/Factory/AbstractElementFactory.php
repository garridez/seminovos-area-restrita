<?php

namespace SnBH\Common\Form\Element\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;

abstract class AbstractElementFactory implements FactoryInterface
{
    public function getApiClient(ContainerInterface $container): ApiClient
    {
        return $container->get(ApiClient::class);
    }
}
