<?php

namespace SnBH\ApiModel;

/**
 * @todo Transformar esse modulo em um projeto separado
 */
class Module
{
    public function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }
}
