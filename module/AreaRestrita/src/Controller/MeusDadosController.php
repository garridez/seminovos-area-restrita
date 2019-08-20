<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Service\AuthManager;
use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;
use Zend\Authentication\AuthenticationService;

class MeusDadosController extends AbstractActionController
{

    protected $container;
    protected $routeParams;
    protected $routeName;

    public function __construct()
    {
        global $container;
        $this->container = $container;

        /**
         * Apenas para mostrar na view a rota
         */
        /* @var $routeMatch \Zend\Router\Http\RouteMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function indexAction()
    {
        $formularioSalvo = false;
        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        // Busca os dados do cadastro
        $dadosCadastro = $cadastrosModel->getCurrent(false);
        $tipoCadastro = 1;

        if ($cadastrosModel->isRevenda()) {
            $dadosForm = new MeusDados\RevendaForm();
            $dadosForm->get('senha')->setAttribute('required', false);
            $dadosForm->get('confirmacaoSenha')->setAttribute('required', false);
            $dadosForm->getInputFilter()->remove('senha')->remove('confirmacaoSenha');
            $request = $this->getRequest();
        } else {
            $dadosForm = new MeusDados\ParticularForm();
            $dadosForm->get('senha')->setAttribute('required', false);
            $dadosForm->get('confirmacaoSenha')->setAttribute('required', false);
            $dadosForm->getInputFilter()->remove('senha')->remove('confirmacaoSenha');
            $request = $this->getRequest();
            $tipoCadastro = 2;
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            $dadosForm->setData($post);
            if ($dadosForm->isValid()) {

                /* @var $apiClient \SnBH\ApiClient\Client */
                $data = $dadosForm->getData();

                $data['tipoCadastro'] = $cadastrosModel->isRevenda() ? 1 : 2;

                #campos não existentes na tabela
                unset($data['confirmacaoSenha']);
                unset($data['submit']);

                #campos que não podem ser alterados
                unset($data['responsavelNome']);
                unset($data['email']);
                unset($data['dataNascimento']);
                unset($data['cpf']);
                unset($data['nomeFantasia']);

                $resPut = $cadastrosModel->put($data);
                if ($resPut->status === 200) {
                    // Busca os dados do cadastro atualizado
                    $dadosCadastro = $cadastrosModel->getCurrent(false);
                    $formularioSalvo = true;
                }
            }
        }

        $dadosForm->populateValues($dadosCadastro);
        
        return new ViewModel([
            'tipoCadastro' => $tipoCadastro,
            'formCadastro' => $dadosForm,
            'idCidade' => $dadosCadastro['idCidade'],
            'formularioSalvo' => $formularioSalvo
        ]);
    }

    public function alterarSenhaAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            /* @var $cadastrosModel Cadastros */
            $cadastrosModel = $this->getContainer()->get(Cadastros::class);

            /* @var $authManager AuthManager  */
            $authManager = $this->container->get(AuthManager::class);

            $post = $request->getPost();

            // Busca os dados do cadastro
            $dadosCadastro = $cadastrosModel->getCurrent(false);

            $tipoCadastro = 2;
            $login = $dadosCadastro['email'];

            if ($cadastrosModel->isRevenda()) {
                $tipoCadastro = 1;
                $login = $dadosCadastro['cnpj'];
            }

            #valida a senha atual do usuraio
            $result = $authManager->login([
                'emailOrCnpj' => $login,
                'usuarioSenha' => $post['senhaAtual'],
                'tipoCadastro' => $tipoCadastro,
                'rememberMe' => false
            ], false);

            if ($result->getCode() !== $result::SUCCESS) {
                return new ViewModel(["erroSenha"=>1]);
            }

            $data['senha'] = $post['senha'];
            $data['tipoCadastro'] = $tipoCadastro;

            $resPut = $cadastrosModel->put($data);
            if ($resPut->status === 200) {
                // Busca os dados do cadastro atualizado
                $dadosCadastro = $cadastrosModel->getCurrent(false);
                return new ViewModel(["sucesso"=>1]);
            }
        }
        return new ViewModel([]);
    }
}