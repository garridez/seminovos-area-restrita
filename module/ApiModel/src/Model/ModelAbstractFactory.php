<?php

namespace SnBH\ApiModel\Model;

use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;

class ModelAbstractFactory implements AbstractFactoryInterface
{
    protected array $namespacesPrefix = [];

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $apiClient = $container->get(ApiClient::class);
        return new $requestedName($apiClient, $container);
    }

    /**
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
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

    public function getPrefix(ContainerInterface $container): array
    {
        if ($this->namespacesPrefix) {
            return $this->namespacesPrefix;
        }
        $config = $container->get('Config');
        $this->namespacesPrefix = (array) $config['SnBH\ApiModel']['model_factory_namespace_prefix'];
        return $this->namespacesPrefix;
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }
}
