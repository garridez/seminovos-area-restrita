<?php

namespace SnBH\ApiModel\Model;

use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use SnBH\ApiModel;

class ModelAbstractFactory implements AbstractFactoryInterface
{

    protected $namespacesPrefix;

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $apiClient = $container->get(ApiClient::class);
        return new $requestedName($apiClient, $container);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $can = false;

        foreach ($this->getPrefix($container) as $prefix) {
            if ($requestedName[0] === $prefix[0] && strpos($requestedName, (string) $prefix) === 0) {
                $can = true;
                break;
            }
        }

        return $can;
    }

    public function getPrefix(ContainerInterface $container)
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
