<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\View\Helper\BodyClass;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use AreaRestrita\Model\Cadastros;

class BodyClassFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $isRevenda = $container->get(Cadastros::class)->isRevenda();

        $route = $container->get('application')
            ->getMvcEvent()
            ->getRouteMatch();

        $params = $route->getParams();

        $controller = $params['controller'];
        $controller = explode('\\', $controller);
        $controller = preg_replace('/Controller$/', '', end($controller));
        $controller = preg_replace('/(.)([A-Z])/', '$1-$2', $controller);

        $classs = [
            // Nome da rota da requisição
            'r-' . str_replace('/', '_', $route->getMatchedRouteName()),
            // Nome da classe do controller
            'c-' . strtolower($controller),
            // Nome da action
            'a-' . $params['action'],
            // Classe que fala se é revenda ou não
            't-' . ($isRevenda ? 'revenda' : 'particular')
        ];

        return new BodyClass(implode(' ', $classs));
    }
}
