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
    /**
     * O parametro $dados deve ser um array com as seguintes chaves:
     *   emailOrCnpj  // Opcional - se não for passado, o campo idCadastro vira obrigatório
     *   idCadastro   // Opcional - se não for passado, o campo emailOrCnpj vira obrigatório
     *   usuarioSenha // Opcional
     *   loginWithoutPassword // Opcional - Se true, será usado o idCadastro para realizar o login sem senha
     *   tipoCadastro // Opcional
     *   rememberMe   // Opcional - Mantém a sessão ativa por 30 dias
     * 
     * @param array $options
     * @return Result
     * @throws \Exception
     */
    public function login($options): Result
    {
        $optionsDefault = [
            'emailOrCnpj' => '',
            'idCadastro' => '',
            'usuarioSenha' => '',
            'loginWithoutPassword' => false,
            'tipoCadastro' => '',
            'rememberMe' => false,
        ];

        $options = array_merge($optionsDefault, $options);

        if ($this->authService->getIdentity() != null) {
            throw new \Exception('Already logged in');
        }
        if (!isset($options['emailOrCnpj']) && !isset($options['idCadastro'])) {
            throw new \Exception('emailOrCnpj ou idCadastro não passados');
        }
        /* @var $authAdapter \AreaRestrita\Service\AuthAdapter */
        $authAdapter = $this->authService->getAdapter();

        $authAdapter->setData([
            'loginWithoutPassword' => $options['loginWithoutPassword'],
            'idCadastro' => $options['idCadastro'],
            'usuarioEmail' => $options['emailOrCnpj'],
            'usuarioSenha' => $options['usuarioSenha'],
            'tipoCadastro' => $options['tipoCadastro'],
        ]);
        $result = $this->authService->authenticate();

        if ($result->getCode() == Result::SUCCESS) {
            if ($options['rememberMe']) {
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
