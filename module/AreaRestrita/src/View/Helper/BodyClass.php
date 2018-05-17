<?php

namespace AreaRestrita\View\Helper;

use Zend\View\Helper\AbstractHelper;

class BodyClass extends AbstractHelper
{

    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function __invoke()
    {
        return $this->class;
    }
}
