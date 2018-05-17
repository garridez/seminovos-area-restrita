<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\View\Helper\UserInfo;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserInfoFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $container->get(Cadastros::class);
        $data = $cadastrosModel->getCurrent();

        return new UserInfo($data);
    }
}
