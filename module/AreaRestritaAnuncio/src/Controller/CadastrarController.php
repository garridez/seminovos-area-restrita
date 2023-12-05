<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestritaAnuncio\Form\Cadastro\CadastroSimplesForm;
use AreaRestritaAnuncio\Form\Cadastro\CadastroCarroBolsoForm;
use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Controller\AuthController;
use AreaRestrita\Form\MeusDados\ParticularForm;
use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\EnviarEmail;
use SnBH\Common\Helper\ValidatorMessages;
use Laminas\Stdlib\Parameters;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

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
                    $data['dataNascimento'] = date('d/m/Y', strtotime(str_replace('/', '-', (string) $data['dataNascimento'])));
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
        $campoCpfOuCnpj = preg_match('/^(\d{3})\.?(\d{3})\.?(\d{3})-?(\d{2})/', (string) $cpfOuCpnj) ? 'cpfResponsavel' : 'cnpj';
        $tipoCadastro = preg_match('/^(\d{3})\.?(\d{3})\.?(\d{3})-?(\d{2})/', (string) $cpfOuCpnj) ? 2 : 1;
        $considerarInativo = false;

        // CPF ou CNPJ retira a pontuação
        if($tipoCadastro == 2){
            preg_match_all('(\d)',(string) $cpfOuCpnj, $matches);
            $cpfOuCpnj = implode($matches[0]);
        }else{
            $cpfOuCpnj = $cpfOuCpnj;
            $considerarInativo = true;
        }


        /** @var Cadastros $cadastrosModel */
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
                'status' => 200,
                'cpfCadastro' => false,
                'tipoCadastro' => $tipoCadastro,
                'title' => 'Method Not Allowed',
                'detail' => 'CPF ou CPNJ não encontrado. Verifique e tente novamente',
            ];
        }

        $dadosCadastro = $dadosCadastro[0];

        $email = $dadosCadastro['email'] ?? null;
        $telefone = $dadosCadastro['telefone2'] && $tipoCadastro == 2 ? $dadosCadastro['telefone2'] : null;
        $dadosCadastroRetorno = [];

        if($mask){
            // Mascara email
            $email = preg_replace('/(.{3})(.{1,3})?(.{2})?(.{3})?(.*)?@(.{2,3})([a-zA-Z0-9]{2,})?\.(.*)/', '$1***$3***$5@$6***.$8', (string) $email);

            // Mascara telefone
            $telefone = preg_replace('/\(?(\d{2})\)?\s?(\d{1})\s?(\d{1})(\d{3})\-?(\d{4})/', '($1) $2 $3***-$5', (string) $telefone);

        }else{
            $dadosCadastroRetorno = $dadosCadastro;
        }

        return ['status' => 200, 'cpfCadastro' => true, 'email' => $email, 'telefone' => $telefone, 'tipoCadastro' => $tipoCadastro, 'dadosCadastro' => $dadosCadastroRetorno];
    }   

    /**
     * Get contact from email
     */
    private function getContatosFromEmail($email, $mask = true)
    {
        $tipoCadastro      = 3;
        $considerarInativo = false;

        /** @var Cadastros $cadastrosModel */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        #verifica se o CPF ou CPNJ informado já foi cadastrado no sistema
        $dadosCadastro = $cadastrosModel->get([
            'email' => $email,
            'checkEmail' => true,
            'considerarInativo' => $considerarInativo
        ]);

        if (!$dadosCadastro || !$dadosCadastro[0]) {
            return [
                'status' => 200,
                'cpfCadastro' => false,
                'tipoCadastro' => $tipoCadastro,
                'detail' => 'E-mail não encontrado. Verifique e tente novamente',
            ];
        }

        $dadosCadastro = $dadosCadastro[0];

        $email = $dadosCadastro['email'] ?? null;
        $telefone = $dadosCadastro['telefone2'] ? $dadosCadastro['telefone2'] : null;

        if($mask){
            // Mascara email
            $email = preg_replace('/(.{3})(.{1,3})?(.{2})?(.{3})?(.*)?@(.{2,3})([a-zA-Z0-9]{2,})?\.(.*)/', '$1***$3***$5@$6***.$8', (string) $email);

            // Mascara telefone
            $telefone = preg_replace('/\(?(\d{2})\)?\s?(\d{1})\s?(\d{1})(\d{3})\-?(\d{4})/', '($1) $2 $3***-$5', (string) $telefone);

        }

        return ['status' => 200, 'cpfCadastro' => true, 'email' => $email, 'telefone' => $telefone, 'tipoCadastro' => $tipoCadastro, 'dadosCadastro' => $dadosCadastro];
    }

    /**
     * Verifica se o capcha para resetar senha é válido
     */
    public function resetPasswordCheckRecaptcha($token)
    {  
        $httpClient = new \Laminas\Http\Client('https://www.google.com/recaptcha/api/siteverify');

        $request = $httpClient->getRequest();
        $httpClient->setMethod('POST');
        $request->setPost(new \Laminas\Stdlib\Parameters([
            'secret' => '6Lcm0A8fAAAAAKHOaaBQDQYUIX4jV07KiYcrvlE_',
            'response' => $token
        ]));
        
        $resposta = $httpClient->send();
                
        if ($resposta->getStatusCode()) {
            $result = json_decode($resposta->getBody(), true) ;
            
            if (!$result['success']) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function getEmailTelefoneFromCpfOuCnpjAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return [];
        }

        $post = $request->getPost();
        $cpfOuCpnj = $post['cpfOuCpnj'];
        $email     = $post['email'];
        $token     = $post['tokenResetarSenha'];

        $retorno = [];

        if(is_null($token) || !$this->resetPasswordCheckRecaptcha($token)){
            $retorno = [
                'status' => 200,
                'cpfCadastro' => false,
                'tipoCadastro' => 5,
                'detail' => 'Desafio do captcha inválido',
            ];
        } else if($cpfOuCpnj != '') {
            $retorno = $this->getContatosFromCpfCnpj($cpfOuCpnj);
        } else if($email != '') {
            $retorno = $this->getContatosFromEmail($email);
        } else { //retorno default
            $retorno = [
                'status' => 200,
                'cpfCadastro' => false,
                'tipoCadastro' => 4,
                'detail' => 'E-mail não encontrado. Verifique e tente novamente',
            ];
        }
        
        if($retorno['status'] != 200){
            return json_encode($retorno);
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
        $email     = $post['email'];

        $retorno = [];

        if($cpfOuCpnj != '') {
            $retornoContato = $this->getContatosFromCpfCnpj($cpfOuCpnj,false);
        } else if($email != '') {
            $retornoContato = $this->getContatosFromEmail($email,false);
        }

        $dadosCadastro = $retornoContato['dadosCadastro'];

        if (!$dadosCadastro) {
            echo json_encode([
                'status' => 400,
                'title' => 'Method Not Allowed',
                'detail' => 'CPF ou CNPJ ou E-mail não encontrado. Verifique e tente novamente',
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
                'cnpj' => $dadosCadastro['cnpj'],
                'nome' => $dadosCadastro['responsavelNome'],
                'emailRemetente' => 'senha@seminovos.com.br',
                'nomeRemetente' => 'SeminovosBH',
                'novaSenha' => $novaSenha,
                'tipoEmail' => 'nova_senha'
            ];

            /* @var $enviarEmailModel EnviarEmail */
            $enviarEmailModel = $this->getContainer()->get(EnviarEmail::class);
            $enviarEmailModel->post($dadosEmail);
        }

        $email = $dadosCadastro['email'];
        $telefone = $dadosCadastro['telefone2'];

        // Mascara email
        $email = preg_replace('/(.{3})(.{1,3})?(.{2})?(.{3})?(.*)?@(.{2,3})([a-zA-Z0-9]{2,})?\.(.*)/', '$1***$3***$5@$6***.$8', (string) $email);

        // Mascara telefone
        $telefone = preg_replace('/\(?(\d{2})\)?\s?(\d{1})\s?(\d{1})(\d{3})\-?(\d{3})(\d{1})/', '($1)$2$3***-***$6', (string) $telefone);

        return new JsonModel(['status' => 200, 'email' => $email, 'telefone' => $telefone]);
    }

    /**
     * Envia o token para o cliente
     *
     * @return JsonModel|array
     */
    public function rememberPassPhoneAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return [];
        }
        $post = $request->getPost();

        $cpfOuCpnj = $post['cpfOuCpnj'];
        $email = $post['email'];

        if($cpfOuCpnj != '') {
            $retornoContato = $this->getContatosFromCpfCnpj($cpfOuCpnj, false);
        } else if($email != '') {
            $retornoContato = $this->getContatosFromEmail($email, false);
        } 

        if($retornoContato['status'] != 200){
            return new JsonModel($retornoContato);
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
        if(!(is_countable($dadosCadastro) ? count($dadosCadastro) : 0)){
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
            'cpfResponsavel' => preg_replace('/[^0-9]/', '', (string) $cpf),
            'checkEmail' => true, //variavél exclui os joins da CadastroDAO.class.php
            'considerarInativo' => 1
        ]);

        $cpfDisponivel = false;
        $emailVinculado = false;
        if(!(is_countable($dadosCadastro) ? count($dadosCadastro) : 0)){
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
                $data['cpfResponsavel'] ??= false;
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

    public function carroBolsoAction()
    {
        $dadosForm = new CadastroCarroBolsoForm();

        $request = $this->getRequest();

        if ($request->isPost()) {
            //var_dump($request); exit;
            $post = $request->getPost();

            $dadosForm->setData($post);

            if ($dadosForm->isValid()) {

                $data = $dadosForm->getData();

                 $mensagem = '<br /><br /><strong>Parceria Seminovos.com</strong><br /><br /> <strong>Nome: </strong>' . $data['responsavelNome'] . '<br /><strong>telefone: </strong>: ' . $data['telefone_2'] . '<br /><strong>Nome do usuário: </strong>: ' . $data['email'] . '<br /><br />Atenciosamente.<br />Equipe SeminovosBH.';

                $dadosEmail = [
                    'mensagem' => $mensagem,
                    'assunto' => 'Carro no bolso',
                    'email' => 'joao@seminovos.com.br',
                    'nome' => 'Carro no bolso',
                    'emailRemetente' => 'contato@seminovos.com.br',
                    'nomeRemetente' => 'SeminovosBH',
                    'layout' => 'blank-nova',
                    'tipoEmail' => 'personalizado_novo',
                    'bcc' => ['felipe@seminovos.com.br']
                ];

                /* @var $enviarEmailModel EnviarEmail */
                $enviarEmailModel = $this->getContainer()->get(EnviarEmail::class);
                $retorno = $enviarEmailModel->post($dadosEmail);

                //return $this->redirect()->toUrl('https://seminovos.com');

                $view = new ViewModel([
                    'formCarroBolso' => $dadosForm,
                    'sucesso' => $retorno['status'] == 200
                ]);

                $this->layout('layout/blank.phtml');

                return $view;

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
                'formCarroBolso' => $dadosForm
            ]);

            $this->layout('layout/blank.phtml');

            return $view;

        }
    }
}
