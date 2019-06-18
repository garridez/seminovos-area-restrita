<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Form\MeusDados\ParticularForm;
use AreaRestrita\Model\EnviarEmail;
use Zend\View\Model\ViewModel;
use AreaRestritaAnuncio\Form\Cadastro;
use AreaRestrita\Model\Cadastros;

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

                /* @var $apiClient \SnBH\ApiClient\Client */
                $data = $dadosForm->getData();

                $data['tipoCadastro'] = 2;

                #verifica se o email informado já foi cadastrado no sistema
                $dadosCadastro = $cadastrosModel->get([
                    'tipoCadastro' => $data['tipoCadastro'],
                    'email' => $data['email'],
                    'checkEmail' => true
                ]);

                if (sizeof($dadosCadastro) > 0) {
                    echo 'Email já cadastrado no sistema!';
                    var_dump($dadosForm->getMessages());
                    die;
                } else {
                    #campos não existentes na tabela
                    unset($data['confirmacaoSenha']);
                    unset($data['submit']);
                    unset($data['dataNascimento']);

                    $resPost = $cadastrosModel->post($data);

                    $this->checkApiError($resPost);

                    echo json_encode($resPost->json());
                    die;
                }
            } else {
                echo 'dados invalidos';
                var_dump($dadosForm->getMessages());
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

        if ($request->isPost()) {
            $post = $request->getPost();

            $email = $post['emailLembrarSenha'];
//            $tipoCadastro = $post['tipoCadastro'];
            $tipoCadastro = 2;

            /* @var $cadastrosModel Cadastros */
            $cadastrosModel = $this->getContainer()->get(Cadastros::class);

            #verifica se o email informado já foi cadastrado no sistema
            $dadosCadastro = $cadastrosModel->get([
                'tipoCadastro' => $tipoCadastro,
                'email' => $email,
                'checkEmail' => true
            ]);

            if ($dadosCadastro && sizeof($dadosCadastro[0]) > 0) {
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
                        'email' => [
                            $dadosCadastro['responsavelNome'] => $dadosCadastro['email'],
                            'senha' => 'senha@seminovosbh.com.br'
                        ],
                        'nome' => $dadosCadastro['responsavelNome'],
                        'emailRemetente' => 'senha@seminovosbh.com.br',
                        'nomeRemetente' => 'SeminovosBH',
                        'tipoEmail' => 'personalizado'
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
            } else {
                echo json_encode([
                    'status' => 400,
                    'title' => 'Method Not Allowed',
                    'detail' => 'Email incorreto. Verifique e tente novamente',
                ]);
                die;
            }
        }
        echo 'Não foi enviado o post!';
        var_dump(__LINE__);
        die;
    }
}
