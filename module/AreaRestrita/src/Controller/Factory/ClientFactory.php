<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller\Factory;

use UsersClient\Client;
use AreaRestrita\Module;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class ClientFactory
 *
 * @package AreaRestrita\Controller\Factory
 * @author italodeveloper <italo.araujo@seminovosbh.com.br>
 * @version 1.0.0
 */
class ClientFactory
{
    /**
     * @param ContainerInterface $container
     * @return Client
     * @throws GuzzleException
     */
    public static function create(ContainerInterface $container): Client
    {
        $sessionContainer = $container->get(Module::SESSION_NAMESPACE);
        /** @var array $sessionAuth pega o array serializado salvo em sessão com as credenciais do @Identity */
        if(isset($sessionContainer->auth)){
            $sessionAuth = unserialize($sessionContainer->auth);
        }

        $client = new Client([
            'base_uri' => $container->get('Config')['UsersModuleApi']['base_uri'] ?? 'http://34.213.61.95',
            //Default para carramento padrão, estilo seminovos
            'clientMode' => $sessionAuth['clientMode'] ?? 2,
            'support' => [
                'user' => $container->get('Config')['UsersModuleApi']['support']['user'],
                'password' => $container->get('Config')['UsersModuleApi']['support']['password']
            ]
        ]);
        
        /** Se tiver logado retorna com os dados do cliente, caso ao contrario nao prejudica o invoke do mesmo */
        if(isset($sessionAuth['apiToken']) && !empty($sessionAuth['apiToken'])){
            /** Carrega o Token do usuario carregado em sesão já na classe cliente */
            $client->setApiToken($sessionAuth['apiToken']);
            /** Seta os dados baicos do usuario adquiridos no login para maior velocidade de carregamento dos dados */
            $client->setUserData($client->get());
        }
        return $client;
    }
}
