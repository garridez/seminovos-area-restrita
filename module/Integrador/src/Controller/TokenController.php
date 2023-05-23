<?php

namespace SnBH\Integrador\Controller;

use SnBH\ApiClient\Client as ApiClient;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\JsonModel;
use Laminas\Authentication\AuthenticationService;
use AreaRestrita\Service\AuthManager;

class TokenController extends AbstractActionController {

    public function fetch() {

        $usuarioEmail = $this->params()->fromQuery('usuarioEmail');
        $usuarioSenha = $this->params()->fromQuery('usuarioSenha');
        $tipoCadastro = $this->params()->fromQuery('tipoCadastro');

        /* @var $container ServiceLocatorInterface */
        global $container;

        /* @var $authService AuthenticationService */
        $authService = $container->get(AuthenticationService::class);
        $authService->clearIdentity();

        /* @var $authManager AuthManager  */
        $authManager = $container->get(AuthManager::class);    

        try {
            $result = $authManager->login([
                'emailOrCnpj' => $usuarioEmail,
                'usuarioSenha' => $usuarioSenha,
                'tipoCadastro' => $tipoCadastro,
                'rememberMe' => false,
            ]);

        }catch(Exception $e) {

            $this->response->setStatusCode(500);
            return new JsonModel([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        
        
        if ($result->getCode() === $result::SUCCESS) {

            $res = $this->getApiClient()->integracaoTokenPost(array('nome'=> "app", 'idCadastro' => $result->getIdentity()))->json();

            return new JsonModel($res["data"]);
            
        } else {

            $this->response->setStatusCode(401);
            return new JsonModel([
                'success' => false,
                'message' => 'Invalid user'
            ]);
        }
    }
}
