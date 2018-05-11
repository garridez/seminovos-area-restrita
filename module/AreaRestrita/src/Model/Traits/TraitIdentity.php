<?php

namespace AreaRestrita\Model\Traits;

use Zend\Authentication\AuthenticationService;

trait TraitIdentity
{

    /**
     * Já deve estar setado pela classe que vai usar esse trait
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $container;

    /**
     * Id do usuário loggado
     * @var int
     */
    private $identity;

    protected function getIdentity()
    {
        if (!$this->identity) {
            /* @var $authService AuthenticationService */
            $authService = $this->container->get(AuthenticationService::class);
            $this->identity = $authService->getIdentity();
        }
        return $this->identity;
    }
}
