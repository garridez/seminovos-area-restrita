<?php

namespace SnBH\Integrador\Controller;

use AreaRestrita\Service\AuthManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\View\Model\JsonModel;

class TokenController extends AbstractActionController
{
    public function fetch()
    {
        $usuarioEmail = $this->params()->fromQuery('usuarioEmail');
        $usuarioSenha = $this->params()->fromQuery('usuarioSenha');
        $tipoCadastro = $this->params()->fromQuery('tipoCadastro');

        /** @var ServiceLocatorInterface $container */
        global $container;

        /** @var AuthenticationService $authService */
        $authService = $container->get(AuthenticationService::class);
        $authService->clearIdentity();

        /** @var AuthManager $authManager */
        $authManager = $container->get(AuthManager::class);

        try {
            $result = $authManager->login([
                'emailOrCnpj' => $usuarioEmail,
                'usuarioSenha' => $usuarioSenha,
                'tipoCadastro' => $tipoCadastro,
                'rememberMe' => false,
            ]);
        } catch (Exception $e) {
            $this->response->setStatusCode(500);
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        if ($result->getCode() === $result::SUCCESS) {
            $res = $this->getApiClient()->integracaoTokenPost(['nome' => "app", 'idCadastro' => $result->getIdentity()])->json();
            $res["data"]["idCadastro"] = $result->getIdentity();

            return new JsonModel($res["data"]);
        } else {
            $this->response->setStatusCode(401);
            return new JsonModel([
                'success' => false,
                'message' => 'Invalid user',
            ]);
        }
    }
}
