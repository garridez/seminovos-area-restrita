<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\View\Helper\BodyClass;
use interop\container\containerinterface;
use Laminas\Authentication\AuthenticationService as AuthService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class BodyClassFactory implements FactoryInterface
{
    public function __invoke(containerinterface $container, $requestedName, ?array $options = null)
    {
        $classs = array_merge(
            $this->getClassByRoute($container),
            $this->getClassByTipoCadastro($container)
        );

        return new BodyClass(implode(' ', $classs));
    }

    protected function getClassByRoute($container)
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
            'r-' . str_replace('/', '_', (string) $route->getMatchedRouteName()),
        // Nome da classe do controller
            'c-' . strtolower($controller),
        // Nome da action
            'a-' . $params['action'],
        ];
    }

    protected function getClassByTipoCadastro($container)
    {
        if (!$container->get(AuthService::class)->hasIdentity()) {
            return [];
        }

        $isRevenda = $container->get(Cadastros::class)->isRevenda();

        return [
            't-' . ($isRevenda ? 'revenda' : 'particular'),
        ];
    }
}
