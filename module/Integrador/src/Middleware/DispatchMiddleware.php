<?php

namespace SnBH\Integrador\Middleware;

use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatchMiddleware implements MiddlewareInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $container = $this->container;

        /** @var Application $application  */
        $application = $container->get('Application');

        $event = $application->getMvcEvent();
        $routeParams = $event->getRouteMatch()->getParams();
        $event->getRouteMatch()->setParam('controller', $routeParams['controllerHandler']);

        /**
         * Realiza manualmente o dispatch do DispatchListener
         *  para executar o controler de acordo com a rota
         */
        $dispatch = $container->get('DispatchListener');
        $resultDispath = $dispatch->onDispatch($event);

        $event->setResult($resultDispath);
        $event->setTarget($application);

        /**
         * Dispara os eventos 'render' e 'finish' para
         *  renderizar a view e finalizar os cabeçalhos.
         */
        $event->setName(MvcEvent::EVENT_RENDER);
        $event->stopPropagation(false); // Clear before triggering

        $events = $application->getEventManager();
        $events->triggerEvent($event);

        $event->setName(MvcEvent::EVENT_FINISH);
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);
        die;
    }
}
