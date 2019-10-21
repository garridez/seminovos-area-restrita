<?php

namespace AreaRestrita\Log\Processors;

use Zend\Log\Processor\ProcessorInterface;

class ApplicationNamespace implements ProcessorInterface
{
    /**
     * Adiciona no começo do array o nome da applicação
     * @param array $event
     * @return array
     */
    public function process(array $event)
    {
        return [
            'application' => 'AreaRestrita'
            ] + $event;
    }
}
