<?php

namespace AreaRestrita\Service;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result as AuthResult;
use SnBH\ApiClient\Client as ApiClient;

class AuthAdapter implements AdapterInterface
{

    protected $apiClient;
    protected $data;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function authenticate(): AuthResult
    {
        $data = $this->data;
        $data['acao'] = 'login';

        /* @var $loginResult \SnBH\ApiClient\Response */
        $loginResult = $this->apiClient->loginPost($data);

        $code = $loginResult->status == 200 ? AuthResult::SUCCESS : AuthResult::FAILURE;

        return new AuthResult($code, (int) $loginResult->getData()[0]['idCadastro']);
    }

    /**
     * Campos a serem passados pelo array:
     *  tipoCadastro
     *  usuarioEmail
     *  usuarioSenha
     * Os campos podem mudar de acordo com a API
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
