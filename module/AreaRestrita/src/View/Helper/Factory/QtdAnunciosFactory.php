<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\View\Helper\QtdAnuncios;
use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Zend\Authentication\AuthenticationService as AuthService;
use Zend\ServiceManager\Factory\FactoryInterface;

class QtdAnunciosFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $idCadastro = $container->get(AuthService::class)->getIdentity();

        $tipoCadastro = $container->get(Cadastros::class)->getCurrent(true)['tipoCadastro'];

        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);
        $data = $apiClient->planosGet([
                'idCadastro' => $idCadastro,
                'tipoCadastro' => $tipoCadastro,
                ], 'anuncios')
            ->getData();

        return new QtdAnuncios($data[0]);
    }
}
