<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class BodyClass extends AbstractHelper
{

    public function __construct(protected $class)
    {
    }

    public function __invoke()
    {
        return $this->class;
    }
}
