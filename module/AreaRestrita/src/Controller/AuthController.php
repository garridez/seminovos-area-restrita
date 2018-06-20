<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Form\Login;
use AreaRestrita\Service\AuthManager;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{

    public function loginAction()
    {
        /* @var $container ServiceLocatorInterface */
        global $container;

        /* @var $authService AuthenticationService */
        $authService = $container->get(AuthenticationService::class);

        if ($authService->hasIdentity()) {
            return $this->redirect()->toRoute('restrito');
        }

        $particularForm = new Login\ParticularForm();
        $revendaForm = new Login\RevendaForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

            /* @var $form \Zend\Form\Form */
            $form = null;
            $particularForm;
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

                $result = $authManager->login($data['usuarioEmail'], $data['usuarioSenha'], $data['tipoCadastro'], $rememberMe);
                if ($result->getCode() === $result::SUCCESS) {
                    return $this->redirect()->toRoute('restrito');
                } else {
                    var_dump('Erro ao entrar');
                    die;
                }
            }
        }

        $viewModel = new ViewModel([
            'particularForm' => $particularForm,
            'revendaForm' => $revendaForm
        ]);
        $this->layout('layout/login.phtml');

        return $viewModel;
    }

    public function logoutAction()
    {
        global $container;

        /* @var $authService AuthenticationService */
        $authService = $container->get(AuthenticationService::class);
        $authService->clearIdentity();

        return $this->redirect()->toRoute('auth');
    }
}
