<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\View\Helper\QtdAnuncios;
use Laminas\Authentication\AuthenticationService as AuthService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;

class QtdAnunciosFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $idCadastro = $container->get(AuthService::class)->getIdentity();

        $tipoCadastro = $container->get(Cadastros::class)->getCurrent(true)['tipoCadastro'];

        /** @var ApiClient $apiClient */
        $apiClient = $container->get(ApiClient::class);
        $data = $apiClient->planosGet([
            'idCadastro' => $idCadastro,
            'tipoCadastro' => $tipoCadastro,
        ], 'anuncios', 60)
            ->getData();

        return new QtdAnuncios($data[0]);
    }
}
