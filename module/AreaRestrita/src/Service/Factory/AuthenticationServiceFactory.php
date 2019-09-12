<?php
declare (strict_types=1);
namespace AreaRestrita\Service\Factory;

use UsersClient\Client;
use AreaRestrita\Module;
use AreaRestrita\Service\AuthAdapter;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

/**
 * Class AuthenticationServiceFactory
 *
 * @package AreaRestrita\Service\Factory
 * @author italodeveloper <italo.araujo@seminvosbh.com.br>
 * @version 1.0.0
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|AuthenticationService
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthenticationService
    {
        $client = new Client([
            'base_uri' =>  $container->get('Config')['UsersModuleApi']['base_uri'],
            //Nao importa o mode passado, poís o login é unico, mas segue o padrao seminovos
            'clientMode' => 2
        ]);
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = $this->getSessionStorage($sessionManager);
        $authAdapter = new AuthAdapter($container, $client);
        return new AuthenticationService($authStorage, $authAdapter);
    }

    /**
     * @param SessionManager $sessionManager
     * @return Session
     * @throws \Exception
     */
    protected function getSessionStorage(SessionManager $sessionManager): Session
    {
        try {
            return new Session(Module::SESSION_NAMESPACE, 'idCadastro', $sessionManager);
        } catch (\Exception $ex) {
            $sessionManager->destroy();
            throw $ex;
        }
    }
}
