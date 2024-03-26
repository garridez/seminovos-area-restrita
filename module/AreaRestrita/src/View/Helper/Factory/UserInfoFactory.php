<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\View\Helper\UserInfo;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class UserInfoFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var Cadastros $cadastrosModel */
        $cadastrosModel = $container->get(Cadastros::class);
        $data = $cadastrosModel->getCurrent();

        return new UserInfo($data);
    }
}
