<?php
declare (strict_types=1);
namespace AreaRestrita\Service;

use UsersClient\Client;
use AreaRestrita\Module;
use Zend\Authentication\Result;
use Interop\Container\ContainerInterface;
use Zend\Authentication\Adapter\AdapterInterface;

/**
 * Class AuthAdapter
 *
 * @package AreaRestrita\Service
 * @author italodeveloper <italo.araujo@seminovosbh.com.br>
 * @version 1.0.0
 */
class AuthAdapter implements AdapterInterface
{
    /*** @var mixed $sessionContainer */
    protected $sessionContainer;
    /*** @var ContainerInterface $container */
    protected $container;
    /*** @var Client $client */
    protected $client;
    /*** @var array $data */
    private $data;

    /**
     * AuthAdapter constructor.
     *
     * @param ContainerInterface $container
     * @param Client $client
     */
    public function __construct(ContainerInterface $container, Client $client)
    {
        $this->client = $client;
        $this->container = $container;
        $this->sessionContainer = $this->container->get(Module::SESSION_NAMESPACE);
    }

    /**
     * Tenta se autentica na API de Usuarios
     * caso retorne algum erro realiza o log
     * e mascara o erro retornando login como false
     *
     * @return Result
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authenticate(): Result
    {
        try {
            $response = $this->client->auth($this->data);
            if(!isset($response['idCadastro']) || !isset($response['userObject'])) {
                return new Result(Result::FAILURE, null);
            }
            /**
             * @var mixed $this->sessionContainer guarda em sessão dados necessarios em outros pontos do modulo
             * não é gravado direto como identidade como idCadastro pois para funcionar perfeitamente é necessario
             * se adicionar o idCadastro + o ApiToken da nova api
             */
            $this->sessionContainer->auth = serialize(['apiToken' => $response['userObject']['apiToken'], 'clientMode' => $response['tipoCadastro'] ?? 2]);
            return new Result(Result::SUCCESS, (int)$response['idCadastro']);
        } catch (\Exception $exception){
            /*** @TODO log de erro na API de usuarios */
            return new Result(Result::FAILURE, null);
        }
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
