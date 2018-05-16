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

class MeusDadosRevendaController extends AbstractActionController
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

        $revendaForm = new MeusDados\RevendaForm();
        $revendaForm->get('senha')->setAttribute('required', false);
        $revendaForm->get('confirmacaoSenha')->setAttribute('required', false);
        $revendaForm->getInputFilter()->remove('senha')->remove('confirmacaoSenha');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();
            $revendaForm->setData($post);
            if ($revendaForm->isValid()) {
                /* @var $apiClient \SnBH\ApiClient\Client */
                $data = $revendaForm->getData();

                #campos não existentes na tabela
                unset($data['confirmacaoSenha']);
                unset($data['submit']);

                $data['tipoCadastro'] = 1;

                $resPut = $cadastrosModel->put($data);
                if ($resPut->status === 200) {
                    // Busca os dados do cadastro atualizado
                    $dadosCadastro = $cadastrosModel->getCurrent(false);
                    $revendaForm->populateValues($dadosCadastro);
                    return new ViewModel([
                        'formCadastro' => $revendaForm
                    ]);
                } else {
                    var_dump($resPut->detail);
                    die;
                }
            } else {
                echo 'não validou os dados';//exit;
                var_dump($revendaForm->getMessages());
                die;
            }
        } else {
            $revendaForm->populateValues($dadosCadastro);
            return new ViewModel([
                'formCadastro' => $revendaForm
            ]);
        }
    }
}