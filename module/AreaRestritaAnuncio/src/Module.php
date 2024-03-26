<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestritaAnuncio;

class Module
{
    final public const SESSION_NAMESPACE = self::class;

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
