<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class ArrayData extends AbstractHelper
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __invoke()
    {
        return $this->data;
    }
}
