<?php

namespace AreaRestrita\Model\Traits;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\ServiceManager;

// phpcs:ignore
trait TraitIdentity
{
    /**
     * Já deve estar setado pela classe que vai usar esse trait
     */
    protected ServiceManager $container;

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
        // phpcs:ignore
        global $container;

        return $container;
    }

    /**
     * @return int
     */
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
