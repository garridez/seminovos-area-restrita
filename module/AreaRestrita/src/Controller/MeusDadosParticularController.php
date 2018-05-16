<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use Zend\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;

class MeusDadosParticularController extends AbstractActionController
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
        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        // Busca os dados do cadastro
        $dadosCadastro = $cadastrosModel->getCurrent(false);

        $particularForm = new MeusDados\ParticularForm();
        $particularForm->get('senha')->setAttribute('required', false);
        $particularForm->get('confirmacaoSenha')->setAttribute('required', false);
        $particularForm->getInputFilter()->remove('senha')->remove('confirmacaoSenha');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();
            $particularForm->setData($post);
            if ($particularForm->isValid()) {
                /* @var $apiClient \SnBH\ApiClient\Client */
                $data = $particularForm->getData();

                #campos não existentes na tabela
                unset($data['confirmacaoSenha']);
                unset($data['submit']);
                #campos que não podem ser alterados
                unset($data['dataNascimento']);
                unset($data['cpf']);

                $data['tipoCadastro'] = 2;

                $resPut = $cadastrosModel->put($data);
                if ($resPut->status === 200) {
                    // Busca os dados do cadastro atualizado
                    $dadosCadastro = $cadastrosModel->getCurrent(false);
                    $particularForm->populateValues($dadosCadastro);
                    return new ViewModel([
                        'formCadastro' => $particularForm
                    ]);
                } else {
                    var_dump($resPut->detail);
                    die;
                }
            } else {
                echo 'não validou os dados';//exit;
                var_dump($particularForm->getMessages());
                die;
            }
        } else {
            $particularForm->populateValues($dadosCadastro);
            return new ViewModel([
                'formCadastro' => $particularForm
            ]);
        }
    }
}