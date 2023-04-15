<?php

namespace AreaRestrita\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Interop\Container\ContainerInterface;
use Interop\Http\ServerMiddleware\DelegateInterface as DelegateI;
use Psr\Http\Message\ServerRequestInterface as ServerRequestI;
use Laminas\Mvc\MvcEvent;

class DispatchMiddleware implements MiddlewareInterface
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $container = $this->container;

        /* @var $application \Laminas\Mvc\Application */
        $application = $container->get('Application');

        $event = $application->getMvcEvent();
        /**
         * Realiza manualmente o dispatch do DispatchListener
         *  para executar o controler de acordo com a rota
         */
        /** @var \Laminas\Mvc\DispatchListener $dispatch */
        $dispatch = $container->get('DispatchListener');
        $controller = $event->getRouteMatch()->getParam('controller_name');
        if ($controller) {
            $event->getRouteMatch()->setParam('controller', $controller);
        }

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
