<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Form\MeusDados\ParticularForm;
use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\EnviarEmail;
use SnBH\Common\Helper\ValidatorMessages;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CadastrarController extends AbstractActionController
{

    public function indexAction()
    {
//        $dadosForm = new Cadastro\CadastroParticularForm();
        $dadosForm = new ParticularForm();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $post = $request->getPost();
            $dadosForm->setData($post);

            if ($dadosForm->isValid()) {
                /* @var $cadastrosModel Cadastros */
                $cadastrosModel = $this->getContainer()->get(Cadastros::class);

                $data = $dadosForm->getData();
                if ($data['dataNascimento']) {
                    $data['dataNascimento'] = date('d/m/Y', strtotime(str_replace('/', '-', $data['dataNascimento'])));
                }

                $data['tipoCadastro'] = 2;
                $resPost = $cadastrosModel->post($data);
                echo json_encode($resPost->json());
                die;
            } else {
                echo json_encode([
                    'status' => 405,
                    'title' => 'Revise as informações inseridas',
                    'detail' => ValidatorMessages::toHTML($dadosForm->getMessages(), $dadosForm),
                ]);
                die;
            }
        } else {
            $email = $this->params('email');
            $dadosForm->get('email')->setValue($email);

            $view = new ViewModel([
                'formCadastro' => $dadosForm
            ]);

            $this->layout('layout/blank.phtml');

            return $view;
        }
    }

    public function rememberPassAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return [];
        }
        $post = $request->getPost();

        $email = $post['emailLembrarSenha'];
        $tipoCadastro = 2;

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        #verifica se o email informado já foi cadastrado no sistema
        $dadosCadastro = $cadastrosModel->get([
            'tipoCadastro' => $tipoCadastro,
            'email' => $email,
            'checkEmail' => true
        ]);

        if (!$dadosCadastro || !$dadosCadastro[0]) {
            echo json_encode([
                'status' => 400,
                'title' => 'Method Not Allowed',
                'detail' => 'Email incorreto. Verifique e tente novamente',
            ]);
            die;
        }

        $dadosCadastro = $dadosCadastro[0];
        // gerar uma nova senha
        $senha = substr(md5(uniqid('')), 0, 7);
        $senha = str_replace('0', '', $senha); // não inserir zeros
        $novaSenha = $senha;


        $retorno = $cadastrosModel->put([
            'senha' => $novaSenha,
            'tipoCadastro' => $tipoCadastro
            ], $dadosCadastro['idCadastro'], null);


        if ($retorno->status == 200) {
            // Envia email pela nova api
            $mensagem = '<br /><br /><strong>Assunto: </strong> Nova senha de acesso<br /><br /> ' . $dadosCadastro['responsavelNome'] . ', conforme solicitado, segue sua nova senha de acesso para o site <a href="http://seminovos.com.br"><font color="orange">seminovos.com.br</font></a><br /><br /><strong>Foi gerada uma nova senha: </strong>' . $senha . '<br /><strong>Login: </strong>: ' . $dadosCadastro['email'] . '<br /><strong>Nome do usuário: </strong>: ' . $dadosCadastro['responsavelNome'] . '<br /><br />Atenciosamente.<br />Equipe SeminovosBH.';

            $dadosEmail = [
                'mensagem' => $mensagem,
                'assunto' => 'Nova senha de acesso',
                'email' => $dadosCadastro['email'],
                'nome' => $dadosCadastro['responsavelNome'],
                'emailRemetente' => 'senha@seminovosbh.com.br',
                'nomeRemetente' => 'SeminovosBH',
                'novaSenha' => $novaSenha,
                'tipoEmail' => 'nova_senha'
            ];

            /* @var $enviarEmailModel EnviarEmail */
            $enviarEmailModel = $this->getContainer()->get(EnviarEmail::class);

            $retorno = $enviarEmailModel->post($dadosEmail);
        }
        if ($retorno instanceof \SnBH\ApiClient\Response) {
            $retorno = $retorno->json();
        }
        echo json_encode($retorno);
        die;
    }
    
    /**
     * Verifica se a email está disponível para cadastro
     * Retorna TRUE se a email estiver disponível
     * Retorna FALSE se a email estiver indisponível
     */
    public function emailDisponivelAction()
    {
        $email = $this->params()->fromRoute('email',false);
        if(!$email){
            return new JsonModel(['status'=> 405, 'detail'=> 'E-mail não informada']); 
        }

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        #verifica se o email informado já foi cadastrado no sistema
        $dadosCadastro = $cadastrosModel->get([
            'email' => $email,
            'checkEmail' => true,
            'considerarInativo' => 1
        ]);

        $emailDisponivel = false;
        if(!sizeof($dadosCadastro)){
            $emailDisponivel =  true;
        }

        return new JsonModel( [
            'status' => 200,
            'emailDisponivel' => $emailDisponivel
        ]);
    }
}
