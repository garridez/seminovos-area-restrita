<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Service\Identity;
use Laminas\View\Model\ViewModel;

class LoginController extends AbstractActionController
{
    /**
     * @todo Implementar: Se receber o email por parametro, checa se o email existe no banco
     * Se existir, pede a senha para entrar
     * Se não existir, redireciona para o usuário criar um cadastro com o email
     * E pode ser que o email não venha, então mande para a tela onde se coloca o email
     */
    public function checkLoginAction()
    {
        $email = $this->params('email');

        /** @var Identity $identity */
        $identity = $this->getContainer()->get(Identity::class);
        if ($identity->hasIdentity()) {
            return $this->redirect()->toRoute('criar-anuncio', [
                'tipo' => $this->params('tipo'),
            ]);
        }

        if ($email === null) {
            $this->redirect()->toRoute(); /* Colocar a rota aqui */
            die;
        }

        $apiClient = $this->getApiClient();
        $res = $apiClient->cadastrosGet([
            'email' => $email,
        ]);
        $this->checkApiError($res);

        if (is_countable($res->getData()) ? count($res->getData()) : 0) {
            $route = 'criar-anuncio/login';
        } else {
            $route = 'criar-anuncio/criar-cadastro';
        }

        return $this->redirect()->toRoute($route, $this->params()->fromRoute());
    }

    public function loginAction()
    {
        $email = $this->params('email');
        return new ViewModel([
            'email' => $email,
        ]);
    }
}
