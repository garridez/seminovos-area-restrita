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
        if (isset($data['acao']) && $data['acao'] === 'login_oauth') {
            return $this->authenticateOauth();
        }
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

    public function authenticateOauth(): AuthResult
    {
        /** @var Response */
        $res = $this->apiClient->loginPost($this->data);
        if ($res->status === 404) {
            $data = $res->getData();
            return new AuthResult(AuthResult::FAILURE_IDENTITY_NOT_FOUND, $data);
        }

        if ($res->status !== 200) {
            return new AuthResult(AuthResult::FAILURE, 0);
        }

        $data = $res->getData()[0];

        return new AuthResult(AuthResult::SUCCESS, (int) $data['idCadastro']);
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
