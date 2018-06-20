<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AreaRestritaAnuncio\Form\Cadastro;
use AreaRestrita\Model\Cadastros;

class CadastrarController extends AbstractActionController
{

    public function indexAction()
    {
        $dadosForm = new Cadastro\CadastroParticularForm();

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

            return new ViewModel([
                'formCadastro' => $dadosForm
            ]);
        }
    }
}
