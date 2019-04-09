<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Model\Planos;
use Zend\View\Model\ViewModel;

class PlanoController extends AbstractActionController
{

    public function indexAction()
    {
        
        $dadosPlanos = $this->getContainer()
            ->get(Planos::class)
            ->getCurrent();

        $viewModel = new ViewModel([
            'planos' => $dadosPlanos,
        ]);

        $viewModel->setTerminal(true);

        return $viewModel;
    }
}
