<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Form\Login;
use AreaRestrita\Service\AuthManager;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use AreaRestrita\Model\Cadastros;

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

    public function loginAutomaticoAction()
    {
        /* @var $container ServiceLocatorInterface */
        global $container;

        $dados = $this->params('dados');

        $dados = str_replace("HBSOVONIMES", "/", $dados);

        $dadosCriptografados = base64_decode($dados);

        list($encryptedText, $encryptionMethod, $secretHash, $iv) = explode("@#", $dadosCriptografados);
        $iv = hex2bin($iv);

        $decryptedText = openssl_decrypt($encryptedText, $encryptionMethod, $secretHash, 0, $iv);

        list($fusoHorario, $idCadastro, $idAnuncio, $dataValidade) = explode(";", $decryptedText);


        if ($dataValidade >= date('Y-m-d')) {

            /* @var $cadastrosModel Cadastros */
            $cadastrosModel = $this->getContainer()->get(Cadastros::class);

            // Busca os dados do cadastro
            $dadosCadastro = $cadastrosModel->get(["idCadastro" => $idCadastro]);

            $rememberMe = true;

            /* @var $authManager AuthManager  */
            $authManager = $container->get(AuthManager::class);

            $result = $authManager->login($dadosCadastro[0]['email'], "123456789", 2, $rememberMe);
            if ($result->getCode() === $result::SUCCESS) {
                return $this->redirect()->toRoute('restrito');
            } else {
                var_dump('Erro ao entrar');
                die;
            }
        } else {
            var_dump('Redirecionar para a tela de login');
//            return $this->redirect()->toRoute('auth');
        }

    }
}
