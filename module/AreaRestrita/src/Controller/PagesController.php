<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita\Controller;

use Laminas\View\Model\ViewModel;

class PagesController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function centralInformacoesAction()
    {
        return new ViewModel();
    }
}
