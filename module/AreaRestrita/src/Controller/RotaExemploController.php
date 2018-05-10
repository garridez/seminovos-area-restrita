<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;

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
        /* @var $routeMatch \Zend\Router\Http\RouteMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
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
            'routeParams' => $this->routeParams
        ]);
    }

    public function outraSubRotaAction()
    {
        return new ViewModel([
            'routeParams' => $this->routeParams
        ]);
    }
}
