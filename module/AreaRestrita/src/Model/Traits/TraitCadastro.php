<?php

namespace AreaRestrita\Model\Traits;

use AreaRestrita\Model\Cadastros;

trait TraitCadastro
{

    /**
     * Já deve estar setado pela classe que vai usar esse trait
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $container;

    /**
     * Dados de Cadastro
     * @var int
     */
    private $data;

    protected function getCadastroData()
    {
        global $container;

        if (!$this->data) {
            $this->data = $container
                ->get(Cadastros::class)
                ->getCurrent();
        }
        return $this->data;
    }
}
