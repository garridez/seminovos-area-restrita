<?php
declare (strict_types=1);
namespace AreaRestrita\Service;

use Zend\Authentication\AuthenticationService;
use Interop\Container\ContainerInterface;

/**
 * Class Identity
 * @package AreaRestrita\Service
 */
class Identity
{
    /*** @var mixed|null $identity */
    protected $identity;
    /*** @var ContainerInterface $container */
    protected $container;

    /**
     * Identity constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->identity = $container->get(AuthenticationService::class)->getIdentity();
    }

    /**
     * @return bool
     */
    public function hasIdentity(): bool
    {
        return (bool)$this->identity;
    }

    /**
     * @return int
     */
    public function getIdentity(): int
    {
        return $this->identity;
    }
}
