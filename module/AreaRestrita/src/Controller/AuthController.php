<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Form\Login;
use AreaRestrita\Service\AuthManager;
use SnBH\Common\Helper\Encrypter;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{

    public function loginAction()
    {
        /* @var $container ServiceLocatorInterface */
        global $container;

        $particularForm = new Login\ParticularForm();
        $revendaForm = new Login\RevendaForm();

        $viewModel = new ViewModel([
            'particularForm' => $particularForm,
            'revendaForm' => $revendaForm
        ]);
        $this->layout('layout/login.phtml');

        /* @var $authService AuthenticationService */
        $authService = $container->get(AuthenticationService::class);

        if ($authService->hasIdentity()) {
            return $this->redirect()->toRoute('restrito');
        }



        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $viewModel;
        }
        $post = $request->getPost();

        /* @var $form \Zend\Form\Form */
        $form = null;
        foreach ([$particularForm, $revendaForm] as $form) {
            if ($form->getName() === $post['type']) {
                break;
            }
        }

        $form->setData($post);

        if ($form->isValid()) {
            /* @var $apiClient \SnBH\ApiClient\Client */
            $data = $form->getData();

            $rememberMe = true;

            /* @var $authManager AuthManager  */
            $authManager = $container->get(AuthManager::class);

            $result = $authManager->login([
                'emailOrCnpj' => $data['usuarioEmail'],
                'usuarioSenha' => $data['usuarioSenha'],
                'tipoCadastro' => $data['tipoCadastro'],
                'rememberMe' => $rememberMe
            ]);
            if ($result->getCode() === $result::SUCCESS) {
                return $this->redirect()->toRoute('restrito');
            }
        }

        $viewModel->setVariable('loginError', true);


        return $viewModel;
    }

    public function logoutAction()
    {
        global $container;

        /* @var $authService AuthenticationService */
        $authService = $container->get(AuthenticationService::class);
        $authService->clearIdentity();

        $url = $container->get('Config')['SnBH']['urls']['site'];

        return $this->redirect()->toUrl($url);
    }

    public function loginAutomaticoAction()
    {
        /* @var $container ServiceLocatorInterface */
        global $container;

        $encryptedText = $this->params('dados');
        $decryptedText = Encrypter::decrypt($encryptedText);

        list($fusoHorario, $idCadastro, $idAnuncio, $dataValidade) = explode(";", $decryptedText);

        if ($dataValidade >= date('Y-m-d')) {

            /* @var $authManager AuthManager  */
            $authManager = $container->get(AuthManager::class);

            $result = $authManager->login([
                'loginWithoutPassword' => true,
                'idCadastro' => $idCadastro,
                'rememberMe' => true
            ]);

            if ($result->getCode() === $result::SUCCESS) {
                return $this->redirect()->toUrl('../meus-veiculos');
            } else {
                return $this->redirect()->toUrl('../entrar#erro');
            }
        } else {
            return $this->redirect()->toRoute('auth');
        }
    }
}
