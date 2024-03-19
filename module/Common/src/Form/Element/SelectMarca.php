<?php

namespace SnBH\Common\Form\Element;

use Laminas\Form\Element\Select;
use SnBH\ApiClient\Client as ApiClient;

class SelectMarca extends Select
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'idMarca', $options = [])
    {
        $options = array_merge([
            'label' => 'Marca',
        ], $options);

        parent::__construct($name, $options);
    }

    /**
     * Seta os modelos de acordo com as marcas
     *
     * @global \Laminas\ServiceManager\ServiceManager $container
     * @param int $idTipo
     * @return $this
     */
    public function setMarcaFromTipo($idTipo)
    {
        // phpcs:ignore
        global $container;
        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);
        $data = $apiClient->marcas([
            'idTipo' => $idTipo,
        ], null, 10000)->getData();

        $modelos = [];
        foreach ($data as $modelo) {
            $modelos[$modelo['idMarca']] = $modelo['marca'];
        }

        $this->setValueOptions($modelos);
        return $this;
    }
}
