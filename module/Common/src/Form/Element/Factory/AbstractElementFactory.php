<?php

namespace SnBH\Common\Form\Element\Factory;

use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use SnBH\ApiClient\Client as ApiClient;

abstract class AbstractElementFactory implements FactoryInterface
{
    public function getApiClient(containerinterface $container): ApiClient
    {
        return $container->get(ApiClient::class);
    }
}
