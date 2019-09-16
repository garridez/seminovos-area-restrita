<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

class ChatController extends AbstractActionController
{

    /**
     * 
     * @global \Zend\ServiceManager\ServiceManager $container
     */
    public function indexAction()
    {
        /* @var $container \Zend\ServiceManager\ServiceLocatorInterface */
        global $container;

        /* @var $authService AuthenticationService */
        $idCadastro = $container->get(AuthenticationService::class)->getIdentity();


        $apiClient = $this->getApiClient();
        $res = $apiClient->mensagensGet([
            'idCadastro' => $idCadastro
        ], null, !true);

        $data = $res->getData();

        foreach ($data as &$cv) {
            $cv['meuIdCadastro'] = (string) $idCadastro;
            $cv['idCadastro'] = (string) $cv['idCadastro'];
        }

        return new JsonModel($data);
        $json = $res->json();
        if ($json) {
            echo '<pre>';
            var_export($json);
        } else {
            echo $res->getBody();
        }
        echo '<hr>';
        var_dump($res->getHttpResponse()->getHeaders()->get('time-application')->toString());
        var_dump($res->getTotalTime());
        die;
        die;
    }

    public function mensagensVeiculoAction()
    {
        $apiClient = $this->getApiClient();
        $idVeiculo = $this->params('idVeiculo');
        $params = $this->params()->fromQuery();

        $res = $apiClient->mensagensGet($params, $idVeiculo);
        $json = $res->json();
        if ($json) {
            echo '<pre>';
            var_export($json);
        } else {
            echo $res->getBody();
        }
        die;
        die;
    }
}
