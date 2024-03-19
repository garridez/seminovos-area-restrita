<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\View\Helper\UserInfo;
use interop\container\containerinterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserInfoFactory implements FactoryInterface
{
    public function __invoke(containerinterface $container, $requestedName, ?array $options = null)
    {
        /** @var Cadastros $cadastrosModel */
        $cadastrosModel = $container->get(Cadastros::class);
        $data = $cadastrosModel->getCurrent();

        return new UserInfo($data);
    }
}
