<?php

namespace AreaRestrita\Log\Processors;

use Laminas\Log\Processor\ProcessorInterface;

class ApplicationNamespace implements ProcessorInterface
{
    /**
     * Adiciona no começo do array o nome da applicação
     *
     * @return array
     */
    public function process(array $event)
    {
        return [
            'application' => 'AreaRestrita',
        ] + $event;
    }
}
