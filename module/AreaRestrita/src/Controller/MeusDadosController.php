<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use UsersClient\Client;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Cadastros;
use Psr\Container\ContainerInterface;

/**
 * Class MeusDadosController
 *
 * @package AreaRestrita\Controller
 * @author italodeveloper <italo.araujo@seminovosbh.com.br>
 * @version 1.0.0
 */
class MeusDadosController extends AbstractActionController
{
    /*** @var ContainerInterface $container */
    protected $container;
    /*** @var Client $client */
    protected $client;
    /*** @var array $routeParams */
    protected $routeParams;
    /*** @var MeusDados\ParticularForm|MeusDados\RevendaForm $dataForm */
    protected $dataForm;
    /*** @var Bcrypt $bcrypt */
    protected $bcrypt;

    /**
     * MeusDadosController constructor.
     *
     * @param ContainerInterface $container
     * @param Client $client
     */
    public function __construct(ContainerInterface $container, Client $client)
    {
        $this->container = $container;
        $this->client = $client;
        /** @var \Zend\Router\Http\RouteMatch $routeMatch Apenas para mostrar na view a rota */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
        $this->dataForm = ((int)$this->client->getConfig()['clientMode'] == 1) ? (new MeusDados\RevendaForm()) : (new MeusDados\ParticularForm());
        $this->bcrypt = new Bcrypt();
    }

    /**
     * @return ViewModel
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function indexAction()
    {
        $this->dataForm->get('senha')
            ->setAttribute('required', false);

        $this->dataForm->get('confirmacaoSenha')
            ->setAttribute('required', false);

        $this->dataForm->getInputFilter()
            ->remove('senha')
            ->remove('confirmacaoSenha');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $this->dataForm->setData($post);
            if ($this->dataForm->isValid()) {
                $post = $post->toArray();
                try {
                    $update = $this->client->update($post);
                } catch (\Exception $exception){
                    /**
                     * @TODO
                     * Log de erro na API de Usuarios
                     */
                }
            }
        }

        $dadosCadastro = $this->client->get(true);
        $this->dataForm->populateValues($dadosCadastro);
        return new ViewModel([
            'tipoCadastro' => (int)$this->client->getConfig()['clientMode'],
            'formCadastro' => $this->dataForm,
            'idCidade' => $dadosCadastro['idCidade']
        ]);
    }

    /**
     * @return ViewModel
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function alterarSenhaAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $dadosCadastro = $this->client->get(true);
            if(!$this->bcrypt->verify($post['senhaAtual'],$dadosCadastro['userObject']['senha'])){
                return new ViewModel(['erroSenha' => 1]);
            }
            try {
                $this->client->resetPassword($post);
                return new ViewModel(['sucesso' => 1]);
            } catch (\Exception $exception){
                return new ViewModel(['erroSenha' => 1]);
            }
        }
        return new ViewModel();
    }
}
