<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use Zend\View\Model\ViewModel;
use Zend\Form\Element;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class FacebookLeadsController extends AbstractActionController 
{
    public function indexAction()
    {
        $leads = "requisição get.";

        if($this->getRequest()->isPost()) {
            $leads = $this->getRequest()->getPost();
        }
        
        $nomeArquivo =  '/data/facebook-leads.txt';
        $logger = new Logger();
        $write = new Stream(getcwd() . $nomeArquivo);
        $logger->addWriter($write);
        $logger->info($leads);
    }
}