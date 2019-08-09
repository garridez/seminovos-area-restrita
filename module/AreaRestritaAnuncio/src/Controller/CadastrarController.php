<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use UsersClient\Client;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use AreaRestrita\Model\EnviarEmail;
use Psr\Container\ContainerInterface;
use UsersClient\Exceptions\ExceptionToModal;
use AreaRestrita\Form\MeusDados\ParticularForm;
use AreaRestrita\Controller\AbstractActionController;

/**
 * Class CadastrarController
 * @package AreaRestritaAnuncio\Controller
 */
class CadastrarController extends AbstractActionController
{
    /*** @var ContainerInterface $container */
    protected $container;
    /*** @var Client $client */
    protected $client;
    /*** @var ParticularForm $form */
    protected $form;

    /**
     * CadastrarController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->form = new ParticularForm();
        $this->client = new Client([
            'base_uri' =>  $container->get('Config')['UsersModuleApi']['base_uri'],
            'clientMode' => 2
        ]);
    }

    /**
     * @return JsonModel|ViewModel
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $post = $request->getPost()->toArray();
            $this->form->setData($post);
            if($this->form->isValid()) {
                try {
                    $response = $this->client->create($post);
                    return new JsonModel([
                        'title' => 'Sucesso',
                        'detail' => 'Cadastrado com sucesso',
                        'messages' => $response['apiToken'],
                        'status' => 200
                    ]);
                } catch (\Exception $exception) {
                    return new JsonModel(ExceptionToModal::manipulate($exception, 'Verifique os dados enviados e tente novamente.'));
                }
            } else {
                $email = $this->params('email');
                $this->form->get('email')->setValue($email);
            }
        }
        $this->layout('layout/blank.phtml');
        return new ViewModel([
            'formCadastro' => $this->form
        ]);
    }

    public function rememberPassAction()
    {
        $request = $this->getRequest();
        if(!$request->isPost()) {
            return new JsonModel(['blank']);
        }
        $post = $request->getPost()->toArray();
        $senhaGerada = str_replace('0', '', substr(md5(uniqid('')), 0, 7));
        try {
            $response = $this->client->resetPasswordMaster([
                'email' =>  $post['emailLembrarSenha'],
                'password' => $senhaGerada,
            ]);
        } catch(\Exception $exception){
            /**
             * @TODO, log de erro
             */
            return new JsonModel(['error' => $exception]);
        }

        try {
            $this->client->auth([
                'login' => $post['emailLembrarSenha'],
                'password' => $senhaGerada
            ]);
        } catch(\Exception $exception){
            return [];
        }
        
        $userData = $this->client->getUserData();
        $mensagem = '<br /><br /><strong>Assunto: </strong> Nova senha de acesso<br /><br /> ' . $userData->getResponsavelNome() . ', conforme solicitado, segue sua nova senha de acesso para o site <a href="http://seminovos.com.br"><font color="orange">seminovos.com.br</font></a><br /><br /><strong>Foi gerada uma nova senha: </strong>' . $senhaGerada . '<br /><strong>Login: </strong>: ' . $userData->getEmail() . '<br /><strong>Nome do usuário: </strong>: ' . $userData->getResponsavelNome() . '<br /><br />Atenciosamente.<br />Equipe SeminovosBH.';

        /** @var EnviarEmail $enviarEmailModel  */
        $enviarEmailModel = $this->container->get(EnviarEmail::class);
        $response = $enviarEmailModel->post([
            'mensagem' => $mensagem,
            'assunto' => 'Nova senha de acesso',
            'email' => [
                (string)$userData->getResponsavelNome() => $userData->getEmail(),
                'senha' => 'senha@seminovosbh.com.br'
            ],
            'nome' =>  $userData->getResponsavelNome(),
            'emailRemetente' => 'senha@seminovosbh.com.br',
            'nomeRemetente' => 'SeminovosBH',
            'tipoEmail' => 'personalizado'
        ]);
        if($response instanceof \SnBH\ApiClient\Response) {
            $response = json_decode($response->json(), true);
        }
        return new JsonModel($response);
    }
}
