<?php


namespace SnBH\Importer\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use SnBH\ApiClient\Client as ApiClient;

class IndexController extends AbstractActionController
{

    protected function getApiClient(): ApiClient
    {
        static $apiClient;

        if (!$apiClient) {
            $apiClient = $this->getEvent()->getApplication()->getServiceManager()->get(ApiClient::class);
        }

        return $apiClient;
    }

    public function indexAction()
    {
        $this->layout()->setTemplate('layout/blank');
        $apiClient = $this->getApiClient();

        //$apiClient->
        $jsonUrl = 'http://dashboard.seminovos.com.br:8088/api';
        $veiculos = json_decode(file_get_contents($jsonUrl), true);

        foreach ($veiculos as $veiculo) {
            var_dump($veiculo);

            $cadastro = $apiClient->cadastrosGet([
                'email' => $veiculo['email'],
            ])->json();

            var_dump($cadastro);
        }
    }
}
