<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\View\Helper\JsMcvPartial;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class JsMcvPartialFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $classs = $this->getClassByRoute($container);

        return new JsMcvPartial($classs);
    }

    protected function getClassByRoute(ContainerInterface $container): array
    {
        $route = $container->get('application')
            ->getMvcEvent()
            ->getRouteMatch();
        // is 404
        if ($route === null) {
            return [];
        }
        $params = $route->getParams();

        $controller = $params['controller'];
        $controller = explode('\\', (string) $controller);
        $controller = preg_replace('/Controller$/', '', end($controller));
        $controller = preg_replace('/(.)([A-Z])/', '$1-$2', $controller);

        return [
            // Nome da rota da requisição
            'route' => 'r-' . str_replace('/', '_', (string) $route->getMatchedRouteName()),
            // Nome da classe do controller
            'controller' => 'c-' . strtolower($controller),
            // Nome da action
            'action' => 'a-' . $params['action'],
        ];
    }
}
