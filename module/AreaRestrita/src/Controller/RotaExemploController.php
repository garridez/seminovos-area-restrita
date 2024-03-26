<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita\Controller;

use Laminas\Router\Http\RouteMatch;
use Laminas\View\Model\ViewModel;

class RotaExemploController extends AbstractActionController
{
    protected $container;
    protected $routeParams;
    protected $routeName;

    public function __construct()
    {
        global $container;
        $this->container = $container;

        /**
         * Apenas para mostrar na view a rota
         */
        /** @var RouteMatch $routeMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function todasRotasAction()
    {
        return new ViewModel([
            'router' => $this->getEvent()->getRouter(),
            'routeParams' => $this->routeParams,
        ]);
    }

    public function indexAction()
    {
        return new ViewModel([
            'parametro' => $this->params('parametro'),
            'routeParams' => $this->routeParams,
        ]);
    }

    public function subRotaAction()
    {
        return new ViewModel([
            'routeParams' => $this->routeParams,
        ]);
    }

    public function outraSubRotaAction()
    {
        return new ViewModel([
            'routeParams' => $this->routeParams,
        ]);
    }

    public function guiaAction()
    {
        return new ViewModel([]);
    }
}
