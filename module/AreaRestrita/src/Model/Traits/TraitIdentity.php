<?php

namespace AreaRestrita\Model\Traits;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\ServiceManager;

trait TraitIdentity
{
    /**
     * Já deve estar setado pela classe que vai usar esse trait
     *
     * @var ServiceManager
     */
    protected $container;

    /**
     * Id do usuário loggado
     *
     * @var int
     */
    private $identity;

    /**
     * @return ServiceManager
     */
    protected function getContainer()
    {
        global $container;

        return $container;
    }

    protected function getIdentity()
    {
        if (!$this->identity) {
            /** @var AuthenticationService $authService */
            $authService = $this->getContainer()->get(AuthenticationService::class);
            $this->identity = $authService->getIdentity();
        }
        return $this->identity;
    }
}
