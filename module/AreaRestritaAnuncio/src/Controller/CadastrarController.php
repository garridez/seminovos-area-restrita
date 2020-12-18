<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestritaAnuncio\Form\Cadastro\CadastroSimplesForm;
use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Controller\AuthController;
use AreaRestrita\Form\MeusDados\ParticularForm;
use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\EnviarEmail;
use SnBH\Common\Helper\ValidatorMessages;
use Zend\Stdlib\Parameters;
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
    private function getContatosFromCpfCnpj($cpfOuCpnj, $mask = true)
    {
        $campoCpfOuCnpj = preg_match('/^(\d{3})\.?(\d{3})\.?(\d{3})-?(\d{2})/', $cpfOuCpnj) ? 'cpfResponsavel' : 'cnpj';
        $tipoCadastro = preg_match('/^(\d{3})\.?(\d{3})\.?(\d{3})-?(\d{2})/', $cpfOuCpnj) ? 2 : 1;
        $considerarInativo = false;

        // CPF ou CNPJ retira a pontuação
        if($tipoCadastro == 2){
            $cpfOuCpnj = preg_match_all('(\d)',$cpfOuCpnj, $matches);
            $cpfOuCpnj = implode($matches[0]);
        }else{
            $cpfOuCpnj = $cpfOuCpnj;
            $considerarInativo = true;
        }
            

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        #verifica se o CPF ou CPNJ informado já foi cadastrado no sistema
        $dadosCadastro = $cadastrosModel->get([
            'tipoCadastro' => $tipoCadastro,
            $campoCpfOuCnpj => $cpfOuCpnj,
            'checkEmail' => true,
            'considerarInativo' => $considerarInativo
        ]);

        if (!$dadosCadastro || !$dadosCadastro[0]) {
            return [
                'status' => 400,
                'title' => 'Method Not Allowed',
                'detail' => 'CPF ou CPNJ não encontrado. Verifique e tente novamente',
            ];
        }

        $dadosCadastro = $dadosCadastro[0];

        $email = $dadosCadastro['email'] ?? null;
        $telefone = $dadosCadastro['telefone2'] ?? null;
        $dadosCadastroRetorno = [];

        if($mask){
            // Mascara email
            $email = preg_replace('/(.{3})(.{1,3})?(.{2})?(.{3})?(.*)?@(.{2,3})([a-zA-Z0-9]{2,})?\.(.*)/', '$1***$3***$5@$6***.$8', $email);

            // Mascara telefone
            $telefone = preg_replace('/\(?(\d{2})\)?\s?(\d{1})\s?(\d{1})(\d{3})\-?(\d{3})(\d{1})/', '($1)$2$3***-***$6', $telefone);

        }else{
            $dadosCadastroRetorno = $dadosCadastro;
        }

        return ['status' => 200, 'email' => $email, 'telefone' => $telefone, 'tipoCadastro' => $tipoCadastro, 'dadosCadastro' => $dadosCadastroRetorno];
    }


    public function getEmailTelefoneFromCpfOuCnpjAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return [];
        }
        $post = $request->getPost();

        $cpfOuCpnj = $post['cpfOuCpnj'];
        $retorno = $this->getContatosFromCpfCnpj($cpfOuCpnj);

        if($retorno['status'] != 200){
            return json_encode($retorno);
            die;
        }

        return new JsonModel($retorno);
    }

    public function rememberPassAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return [];
        }


        $post = $request->getPost();

        $cpfOuCpnj = $post['cpfOuCpnj'];
        $retornoContato = $this->getContatosFromCpfCnpj($cpfOuCpnj, false);

        $dadosCadastro = $retornoContato['dadosCadastro'];

        if (!$dadosCadastro) {
            echo json_encode([
                'status' => 400,
                'title' => 'Method Not Allowed',
                'detail' => 'CPF ou CNPJ não encontrado. Verifique e tente novamente',
            ]);
            die;
        }

        // gerar uma nova senha
        $senha = substr(md5(uniqid('')), 0, 7);
        $senha = str_replace('0', '', $senha); // não inserir zeros
        $novaSenha = $senha;

        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        $retorno = $cadastrosModel->put([
            'tipoCadastro' => $retornoContato['tipoCadastro'],
            'senha' => $novaSenha
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

        $email = $dadosCadastro['email'];
        $telefone = $dadosCadastro['telefone2'];

        // Mascara email
        $email = preg_replace('/(.{3})(.{1,3})?(.{2})?(.{3})?(.*)?@(.{2,3})([a-zA-Z0-9]{2,})?\.(.*)/', '$1***$3***$5@$6***.$8', $email);

        // Mascara telefone
        $telefone = preg_replace('/\(?(\d{2})\)?\s?(\d{1})\s?(\d{1})(\d{3})\-?(\d{3})(\d{1})/', '($1)$2$3***-***$6', $telefone);

        return new JsonModel(['status' => 200, 'email' => $email, 'telefone' => $telefone]);
    }

    /**
     * Envia o token para o cliente
     *
     * @return Json
     */
    public function rememberPassPhoneAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return [];
        }
        $post = $request->getPost();

        $cpfOuCpnj = $post['cpfOuCpnj'];
        $retornoContato = $this->getContatosFromCpfCnpj($cpfOuCpnj, false);

        if($retornoContato['status'] != 200){
            return new JsonModel($retornoContato);
            die;
        }

        $telefone = $retornoContato['telefone'];

        /* @var $enviarEmailModel EnviarEmail */
        $apiClient = $this->getApiClient();

        $retorno = $apiClient->smsPost(['telefone' => $telefone])->json();

        return new JsonModel(['status' => 200, 'retorno' => $retorno]);
    }

    /**
     * Valida o Token Sms para restaurar senha
     */
    public function validateTokenAction()
    {
        $request = $this->getRequest()->getPost();
        $token = $request['token'];

        $apiClient = $this->getApiClient();

        $retorno = $apiClient->smsGet(['token' => $token])->json();

        return new JsonModel($retorno);
    }


    /**
     * Salva nova senha do usuário
     */
    public function rememberPassSaveAction()
    {
        $request = $this->getRequest()->getPost();
        $apiClient = $this->getApiClient();

        $retorno = $apiClient->smsDelete([
            'senha' => $request['senha'],
            'idCadastro' => $request['idCadastro']
            ])->json();

        return new JsonModel($retorno);
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
    /**
     * Verifica se a cpf está disponível para cadastro
     * Retorna TRUE se a cpf estiver disponível
     * Retorna FALSE se a cpf estiver indisponível
     */
    public function cpfDisponivelAction()
    {
        $cpf = $this->params()->fromRoute('cpf',false);
        if(!$cpf){
            return new JsonModel(['status'=> 405, 'detail'=> 'CPF não informado']);
        }

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        #verifica se o email informado já foi cadastrado no sistema
        $dadosCadastro = $cadastrosModel->get([
            'cpfResponsavel' => preg_replace('/[^0-9]/', '', $cpf),
            'checkEmail' => true, //variavél exclui os joins da CadastroDAO.class.php
            'considerarInativo' => 1
        ]);

        $cpfDisponivel = false;
        $emailVinculado = false;
        if(!sizeof($dadosCadastro)){
            $cpfDisponivel =  true;
        }else{
            $emailVinculado = $dadosCadastro[0]['email'];
        }

        return new JsonModel( [
            'status' => 200,
            'cpfDisponivel' => $cpfDisponivel,
            'emailVinculado' => $emailVinculado
        ]);
    }

    public function cadastroSimplesAction()
    {

        $dadosForm = new CadastroSimplesForm();

        $request = $this->getRequest();

        if ($request->isPost()) {
            //var_dump($request);
            $post = $request->getPost();

            $dadosForm->setData($post);

            if ($dadosForm->isValid()) {
                /* @var $cadastrosModel Cadastros */
                $cadastrosModel = $this->getContainer()->get(Cadastros::class);

                $data = $dadosForm->getData();

                $data['tipoCadastro'] = 2;
                $data['idCidade'] = 2700;
                $data['idEstado'] = 11;
                $data['cpfResponsavel'] = $data['cpfResponsavel'] ?? false;
                $data['cadastroSimplificado'] = 1;

                if (!$data['cpfResponsavel']) {
                     unset($data['cpfResponsavel']);
                 }

                $resPost = $cadastrosModel->post($data);
                if ($resPost->status === 200) {
                    // Redireciona internamente para o login
                    $this->request->setPost(new Parameters([
                        'type' => 'login-particular-form',
                        'usuarioEmail' => $data['email'],
                        'usuarioSenha' => $data['senha'],
                        'tipoCadastro' => '2',
                    ]));
                    return $this->forward()
                        ->dispatch(AuthController::class, [
                        'action' => 'login',
                    ]);

                }
                $this->layout('layout/blank.phtml');
                return new ViewModel([
                    'formCadastro' => $dadosForm,
                    'erro' => $resPost->json()['detail'],
                ]);
            } else {
                echo json_encode([
                    'status' => 405,
                    'title' => 'Revise as informações inseridas',
                    'detail' => ValidatorMessages::toHTML($dadosForm->getMessages(), $dadosForm),
                ]);
                die;
            }
        } else {

            $view = new ViewModel([
                'formCadastro' => $dadosForm
            ]);

            $this->layout('layout/blank.phtml');

            return $view;

        }
    }
}
