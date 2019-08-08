<?php
declare (strict_types=1);
namespace AreaRestrita\Controller;

use AreaRestrita\Form\Login;
use AreaRestrita\Service\AuthManager;
use Psr\Container\ContainerInterface;
use SnBH\Common\Helper\Encrypter;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;

/**
 * Class AuthController
 * @package AreaRestrita\Controller
 */
class AuthController extends AbstractActionController
{
    /*** @var ContainerInterface $container */
    protected $container;
    /*** @var mixed|AuthenticationService $authService */
    protected $authService;
    /*** @var AuthManager|mixed $authManager */
    protected $authManager;
    /*** @var Login\ParticularForm $particularForm */
    protected $particularForm;
    /*** @var Login\RevendaForm $revendaForm */
    protected $revendaForm;

    /**
     * AuthController constructor.
     * @param ContainerInterface $container
     * @author italodeveloper <italo.araujo@seminovosbh.com.br>
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->authService = $container->get(AuthenticationService::class);
        $this->authManager = $this->container->get(AuthManager::class);
        $this->particularForm = new Login\ParticularForm();
        $this->revendaForm = new Login\RevendaForm();
    }

    /**
     * @return Response|ViewModel
     */
    public function loginAction()
    {
        $this->layout('layout/login.phtml');
        if($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('restrito');
        }
        $request = $this->getRequest();
        if(!$request->isPost()) {
            return new ViewModel([
                'particularForm' => $this->particularForm,
                'revendaForm' => $this->revendaForm
            ]);
        }
        $postRequest = $request->getPost();
        $form = ($postRequest['type'] == 'login-revenda-form') ? $this->revendaForm : $this->particularForm;
        $form->setData($postRequest);
        if(!$form->isValid()) {
            return new ViewModel([
                'particularForm' => $this->particularForm,
                'revendaForm' => $this->revendaForm
            ]);
        }
        $dataForm = $form->getData();
        $loginClient = [
            'login' => ($dataForm['tipoCadastro'] == 1) ? str_replace(['.', ',', '-', '/'], '', $dataForm['usuarioEmail']) : $dataForm['usuarioEmail'],
            'password' => $dataForm['usuarioSenha'],
            'rememberMe' => true
        ];
        $result = $this->authManager->login($loginClient);
        if($result->getCode() === $result::SUCCESS) {
            return $this->redirect()->toRoute('restrito');
        }
        return $this->redirect()->toUrl('../entrar#erro');
    }

    /**
     * @return Response
     */
    public function logoutAction(): Response
    {
        $this->authService->clearIdentity();
        $url = $this->container->get('Config')['SnBH']['urls']['site'];
        return $this->redirect()->toUrl($url);
    }

    /**
     * @return Response
     */
    public function loginAutomaticoAction(): Response
    {
        $encryptedText = $this->params('dados');
        $decryptedText = Encrypter::decrypt($encryptedText);
        list($fusoHorario, $idCadastro, $idAnuncio, $dataValidade) = explode(";", $decryptedText);
        if($dataValidade < date('Y-m-d')) {
            return $this->redirect()->toRoute('auth');
        }
        $result = $this->authManager->login([
            'loginWithoutPassword' => true,
            'idCadastro' => $idCadastro,
            'rememberMe' => true
        ]);
        if($result->getCode() === $result::SUCCESS) {
            return $this->redirect()->toUrl('../meus-veiculos');
        }
        return $this->redirect()->toUrl('../entrar#erro');
    }
}
