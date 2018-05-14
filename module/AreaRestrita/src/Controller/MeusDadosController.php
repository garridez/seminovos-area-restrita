<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 11/05/2018
 * Time: 14:15
 */

namespace AreaRestrita\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;

class MeusDadosController extends AbstractActionController
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
        $particularForm = new MeusDados\ParticularForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $particularForm->setData($post);
            if ($particularForm->isValid()) {
                /* @var $apiClient \SnBH\ApiClient\Client */
                $data = $particularForm->getData();

                var_dump($data);
                exit;
            } else {
                echo 'não validou os dados';//exit;
                var_dump($particularForm->getMessages());
                die;
            }
        }
        $form = new Form\MeusDados\ParticularForm();
        return new ViewModel([
            'formCadastro' => $form
        ]);
    }
}