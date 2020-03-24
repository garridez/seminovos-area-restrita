<?php

namespace SnBH\Integrador\Controller;

use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        return new JsonModel([
            'method' => __METHOD__
        ]);
    }
}
