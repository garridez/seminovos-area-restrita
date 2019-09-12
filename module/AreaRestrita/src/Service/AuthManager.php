<?php
declare (strict_types=1);
namespace AreaRestrita\Service;

use Zend\Authentication\Result;
use Zend\Session\SessionManager;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use AreaRestrita\Service\AuthAdapter as Adapter;

/**
 * Class AuthManager
 * @package AreaRestrita\Service
 */
class AuthManager
{
    /*** @var ContainerInterface $container */
    private $container;
    /*** @var mixed|AuthenticationService $authService */
    private $authService;
    /*** @var mixed|SessionManager $sessionManager */
    private $sessionManager;

    /**
     * AuthManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->authService = $container->get(AuthenticationService::class);
        $this->sessionManager = $container->get(SessionManager::class);
    }

    /**
     * @param array $login
     * @return Result
     */
    public function login(array $login): Result
    {
        /* @var Adapter $authAdapter */
        $authAdapter = $this->authService->getAdapter();

        $authAdapter->setData($login);
        $result = $this->authService->authenticate();

        if ($result->getCode() == Result::SUCCESS && $login['rememberMe']) {
            //$this->sessionManager->rememberMe(60 * 60 * 24 * 30);
        }
        return $result;
    }


    public function logout()
    {
        $this->authService->clearIdentity();
    }
}
