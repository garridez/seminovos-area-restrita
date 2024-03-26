<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class BodyClass extends AbstractHelper
{
    public function __construct(protected string $class)
    {
    }

    public function __invoke(): string
    {
        return $this->class;
    }
}
