<?php

namespace AreaRestrita\Model\Traits;

use Laminas\Authentication\AuthenticationService;

trait TraitIdentity
{

    /**
     * Já deve estar setado pela classe que vai usar esse trait
     *
     * @var \Laminas\ServiceManager\ServiceManager
     */
    protected $container;

    /**
     * Id do usuário loggado
     * @var int
     */
    private $identity;

    /**
     * @return \Laminas\ServiceManager\ServiceManager
     */
    protected function getContainer()
    {
        global $container;

        return $container;
    }

    protected function getIdentity()
    {
        if (!$this->identity) {
            /* @var $authService AuthenticationService */
            $authService = $this->getContainer()->get(AuthenticationService::class);
            $this->identity = $authService->getIdentity();
        }
        return $this->identity;
    }
}
