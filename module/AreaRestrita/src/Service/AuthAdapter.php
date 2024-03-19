<?php

namespace AreaRestrita\Service;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result as AuthResult;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Response;

class AuthAdapter implements AdapterInterface
{
    /** @var array */
    protected $data;

    public function __construct(protected ApiClient $apiClient)
    {
    }

    public function authenticate(): AuthResult
    {
        $data = $this->data;
        if ($data['loginWithoutPassword']) {
            return $this->authenticateNoPassword();
        }
        return $this->authenticateWithPassword();
    }

    public function authenticateWithPassword(): AuthResult
    {
        $data = $this->data;
        $data['acao'] = 'login';

        /** @var Response $loginResult */
        $loginResult = $this->apiClient->loginPost($data);

        $code = $loginResult->status == 200 ? AuthResult::SUCCESS : AuthResult::FAILURE;
        $identity = false;
        if ($code === AuthResult::SUCCESS) {
            $identity = (int) $loginResult->getData()[0]['idCadastro'];
        }
        return new AuthResult($code, $identity);
    }

    public function authenticateNoPassword(): AuthResult
    {
        $res = $this->apiClient->cadastrosGet([], $this->data['idCadastro']);

        $code = $res->status == 200 ? AuthResult::SUCCESS : AuthResult::FAILURE;

        return new AuthResult($code, (int) $this->data['idCadastro']);
    }

    /**
     * Campos a serem passados pelo array:
     *  tipoCadastro
     *  usuarioEmail
     *  usuarioSenha
     * Os campos podem mudar de acordo com a API
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
