<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\Select;
use SnBH\ApiClient\Client as ApiClient;

class SelectModelo extends Select
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'idModelo', $options = [])
    {
        $options = array_merge([
            'label' => 'Modelo',
        ], $options);

        parent::__construct($name, $options);
    }

    /**
     * Seta os modelos de acordo com as marcas
     *
     * @global \Laminas\ServiceManager\ServiceManager $container
     * @param int $idMarca
     * @return $this
     */
    public function setModelosFromMarca($idMarca)
    {
        // phpcs:ignore
        global $container;
        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);
        $data = $apiClient->modelos([
            'idMarca' => $idMarca,
        ], null, 10000)->getData();
        $modelos = [];
        foreach ($data as $modelo) {
            $modelos[$modelo['idModelo']] = $modelo['modelo'];
        }

        $this->setValueOptions($modelos);
        return $this;
    }
}
