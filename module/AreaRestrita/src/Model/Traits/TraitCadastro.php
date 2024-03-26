<?php

namespace AreaRestrita\Model\Traits;

use AreaRestrita\Model\Cadastros;
use Laminas\ServiceManager\ServiceManager;

// phpcs:ignore
trait TraitCadastro
{
    /**
     * Já deve estar setado pela classe que vai usar esse trait
     */
    protected ServiceManager $container;

    /**
     * Dados de Cadastro
     *
     * @var int
     */
    private $data;

    /**
     * @return array
     */
    protected function getCadastroData()
    {
        // phpcs:ignore
        global $container;

        if (!$this->data) {
            $this->data = $container
                ->get(Cadastros::class)
                ->getCurrent();
        }
        return $this->data;
    }
}
