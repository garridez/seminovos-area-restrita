<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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

        if ($email === null) {
            $this->redirect()->toRoute(/* Colocar a rota aqui */);
            die;
        }
        // verificar a existencia do email
        var_dump(__METHOD__ . ':' . __LINE__);
        die;
    }

    public function loginAction()
    {
        return new ViewModel();
    }
}
