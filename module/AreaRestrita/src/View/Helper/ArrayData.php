<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class ArrayData extends AbstractHelper
{
    public function __construct(protected array $data)
    {
    }

    public function __invoke(): array
    {
        return $this->data;
    }
}
