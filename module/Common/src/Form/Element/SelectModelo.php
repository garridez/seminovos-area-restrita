<?php

namespace SnBH\Common\Form\Element;

use SnBH\ApiClient\Client as ApiClient;
use Zend\Form\Element\Select;

class SelectModelo extends Select
{

    public function __construct($name = 'idModelo', $options = array())
    {
        $options = array_merge([
            'label' => 'Modelo',
            ], $options);

        parent::__construct($name, $options);
    }
    /**
     * 
     * Seta os modelos de acordo com as marcas
     * 
     * @global \Zend\ServiceManager\ServiceManager $container
     * @param int $idMarca
     */
    public function setModelosFromMarca($idMarca)
    {
        global $container;
        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);
        $data = $apiClient->modelos([
                'idMarca' => $data['idMarca']
                ], null, true)->getData();
        $modelos = [];
        foreach ($data as $modelo) {
            $modelos[$modelo['idModelo']] = $modelo['modelo'];
        }
        
        $this->setValueOptions($modelos);
        return $this;
    }
}
