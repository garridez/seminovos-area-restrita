<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Form\Login;
use AreaRestrita\Service\AuthManager;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;

class AuthController extends AbstractActionController
{

    public function loginAction()
    {
        /* @var $container ServiceLocatorInterface */
        global $container;
        
         /* @var $authService AuthenticationService */
        $authService = $container->get(AuthenticationService::class);
        $authService->clearIdentity();

        $redirect = $this->getRequest()->getQuery('redirect', false);
        /** @var SessionManager $sessionManager */
        $sessionManager = $container->get(SessionManager::class);
        $sessionStorage = $sessionManager->getStorage();
        if ($redirect) {
            $redirect = base64_decode($redirect);
            $sessionStorage->redirect = $redirect;
        }
        if ($sessionStorage->redirect) {
            $redirect = $sessionStorage->redirect;
        }

        $particularForm = new Login\ParticularForm();
        $revendaForm = new Login\RevendaForm();

        $viewModel = new ViewModel([
            'particularForm' => $particularForm,
            'revendaForm' => $revendaForm,
            'cadastroSimples' => $this->params()->fromQuery('cadastro-simples', false),
        ]);
        $this->layout('layout/login.phtml');

        /* @var $authService AuthenticationService */
        $authService = $container->get(AuthenticationService::class);

        if ($authService->hasIdentity()) {
            if ($redirect) {
                $sessionStorage->redirect = null;
                return $this->redirect()->toUrl($redirect);
            }
            return $this->redirect()->toRoute('restrito');
        }



        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $viewModel;
        }
        $post = $request->getPost();


        if (!isset($post['token']) || !$post['token']) {
            $viewModel->setVariable('loginError', true);
            $viewModel->setVariable('captchaError', true);
            return $viewModel;
        }

        $httpClient = new \Zend\Http\Client('https://www.google.com/recaptcha/api/siteverify');

        $request = $httpClient->getRequest();
        $httpClient->setMethod('POST');
        $request->setPost(new \Zend\Stdlib\Parameters([
            'secret' => '6Lcm0A8fAAAAAKHOaaBQDQYUIX4jV07KiYcrvlE_',
            'response' => $post['token']
        ]));
        
        $resposta = $httpClient->send();
        
        
        if ($resposta->getStatusCode()) {
            $result = json_decode($resposta->getBody(), true) ;
            if (!$result['success']) {
                $viewModel->setVariable('loginError', true);
                $viewModel->setVariable('captchaError', json_encode($result['error-codes']));
                return $viewModel;
            }
            
        } else {
            $viewModel->setVariable('loginError', true);
            $viewModel->setVariable('captchaError', true);
            return $viewModel;
        }

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
                if ($redirect) {
                    $sessionStorage->redirect = null;
                    return $this->redirect()->toUrl($redirect);
                }
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

    /**
     * Recebe por parametro na url a os dados de login criptografados
     *  Se estiver tudo certo, faz o login sem usar senha
     *  Isso acontece principalmente quando o usuário recebe um email
     * 
     * @return \Zend\Http\Response
     */
    public function loginAutomaticoAction()
    {
        $encryptedText = $this->params('dados');

        $dataRes = $this->getApiClient()->crypterGet([
            'data' => $encryptedText
        ]);

        if ($dataRes->status !== 200) {
            return $this->redirect()->toRoute('auth');
        }
        $data = json_decode($dataRes->getData(), true);

        /**
         * É enviado o "time" de quando o encrypt é criado
         *  Se tiver mais de 5 dias, então não loga
         */
        $maxDays = 60 * 60 * 24 * 5; // 5 dias em segundos
        if ((time() - $data['time']) > $maxDays) {
            return $this->redirect()->toRoute('auth');
        }

        /* @var $authService AuthenticationService */
        $authService = $this->getContainer()->get(AuthenticationService::class);

        if ($authService->hasIdentity()) {
            if (isset($data['url']) && $data['url']) {
                return $this->redirect()->toUrl($data['url']);
            }
            return $this->redirect()->toRoute('auth');
        }

        /* @var $authManager AuthManager  */
        $authManager = $this->getContainer()->get(AuthManager::class);

        $result = $authManager->login([
            'loginWithoutPassword' => true,
            'idCadastro' => $data['idCadastro'],
            'rememberMe' => true,
        ]);

        if ($result->getCode() === $result::SUCCESS) {
            if (isset($data['url']) && $data['url']) {
                return $this->redirect()->toUrl($data['url']);
            }
            return $this->redirect()->toRoute('auth');
        }
    }
}
