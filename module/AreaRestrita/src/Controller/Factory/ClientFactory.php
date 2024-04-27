<?php
// phpcs:ignoreFile
/**
 * @link      http://github.com/laminas/laminas-servicemanager for the canonical source repository
 */

namespace AreaRestrita\Controller\Factory;

use AreaRestrita\Module;
use Psr\Container\ContainerInterface;
use UsersClient\Client;

class ClientFactory
{
    public static function create(ContainerInterface $container): Client
    {
        $sessionAuth = [];
        $sessionContainer = $container->get(Module::SESSION_NAMESPACE);
        /** @var array $sessionAuth pega o array serializado salvo em sessão com as credenciais do @Identity */
        if (isset($sessionContainer->auth)) {
            $sessionAuth = unserialize($sessionContainer->auth);
        }

        $client = new Client([
            'base_uri' => $container->get('Config')['UsersModuleApi']['base_uri'] ?? 'http://34.213.61.95',
            //Default para carramento padrão, estilo seminovos
            'clientMode' => $sessionAuth['clientMode'] ?? 2,
            'support' => [
                'user' => $container->get('Config')['UsersModuleApi']['support']['user'],
                'password' => $container->get('Config')['UsersModuleApi']['support']['password'],
            ],
        ]);

        /** Se tiver logado retorna com os dados do cliente, caso ao contrario nao prejudica o invoke do mesmo */
        if (isset($sessionAuth['apiToken']) && !empty($sessionAuth['apiToken'])) {
            /** Carrega o Token do usuario carregado em sesão já na classe cliente */
            $client->setApiToken($sessionAuth['apiToken']);
            /** Seta os dados baicos do usuario adquiridos no login para maior velocidade de carregamento dos dados */
            $client->setUserData($client->get());
        }
        return $client;
    }
}
