<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class ArrayData extends AbstractHelper
{
    public function __construct(protected $data)
    {
    }

    public function __invoke()
    {
        return $this->data;
    }
}
