<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ChatCriarAnuncioController extends AbstractActionController
{
    public function indexAction()
    {
        //$this->layout('layout/criar-anuncio');
        return new ViewModel([]);
    }
}
