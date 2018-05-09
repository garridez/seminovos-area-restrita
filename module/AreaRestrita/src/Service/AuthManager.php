<?php

namespace AreaRestrita\Service;

use SnBH\ApiClient\Client as ApiClient;
use Zend\Authentication\AuthenticationService as AuthService;
use Zend\Authentication\Result;
use Zend\Session\SessionManager;

class AuthManager
{

    private $authService;
    private $sessionManager;
    private $apiClient;

    public function __construct(AuthService $authService, SessionManager $sessionManager, ApiClient $apiClient)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->apiClient = $apiClient;
    }

    public function login($emailOrCnpj, $usuarioSenha, $tipoCadastro, $rememberMe): Result
    {
        if ($this->authService->getIdentity() != null) {
            throw new \Exception('Already logged in');
        }

        /* @var $authAdapter \AreaRestrita\Service\AuthAdapter */
        $authAdapter = $this->authService->getAdapter();

        $authAdapter->setData([
            'usuarioEmail' => $emailOrCnpj,
            'usuarioSenha' => $usuarioSenha,
            'tipoCadastro' => $tipoCadastro
        ]);
        $result = $this->authService->authenticate();

        if ($result->getCode() == Result::SUCCESS) {
            if ($rememberMe) {
                // Session cookie will expire in 1 month (30 days).
                $this->sessionManager->rememberMe(60 * 60 * 24 * 30);
            }
        }

        return $result;
    }

    public function logout()
    {
        $this->authService->clearIdentity();
    }
}
