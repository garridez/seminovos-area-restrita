<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use AreaRestrita\Service\Identity;

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

        /* @var $identity Identity */
        $identity = $this->getContainer()->get(Identity::class);
        if ($identity->hasIdentity()) {

            return $this->redirect()->toRoute('criar-anuncio', [
                    'tipo' => $this->params('tipo'),
            ]);
        }

        if ($email === null) {
            $this->redirect()->toRoute(/* Colocar a rota aqui */);
            die;
        }



        $apiClient = $this->getApiClient();
        $res = $apiClient->cadastrosGet([
            'email' => $email
        ]);
        $this->checkApiError($res);

        if (sizeof($res->getData())) {
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
            'email' => $email
        ]);
    }
}
