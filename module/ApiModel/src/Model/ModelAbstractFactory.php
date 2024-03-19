<?php

namespace SnBH\ApiModel\Model;

use interop\container\containerinterface;
use Laminas\ServiceManager\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiModel;

class ModelAbstractFactory implements AbstractFactoryInterface
{
    protected $namespacesPrefix;

    public function __invoke(containerinterface $container, $requestedName, $options = null)
    {
        $apiClient = $container->get(ApiClient::class);
        return new $requestedName($apiClient, $container);
    }

    public function canCreate(containerinterface $container, $requestedName)
    {
        $can = false;

        foreach ($this->getPrefix($container) as $prefix) {
            if ($requestedName[0] === $prefix[0] && str_starts_with($requestedName, (string) $prefix)) {
                $can = true;
                break;
            }
        }

        return $can;
    }

    public function getPrefix(containerinterface $container)
    {
        if ($this->namespacesPrefix) {
            return $this->namespacesPrefix;
        }
        $config = $container->get('Config');
        $this->namespacesPrefix = (array) $config[ApiModel::class]['model_factory_namespace_prefix'];
        return $this->namespacesPrefix;
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
    }
}
