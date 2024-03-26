<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestrita\Controller;

use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;

class FacebookLeadsController extends AbstractActionController
{
    public function indexAction()
    {
        $leads = "requisição get.";

        if ($this->getRequest()->isPost()) {
            $leads = $this->getRequest()->getPost();
        }

        $nomeArquivo = '/data/facebook-leads.txt';
        $logger = new Logger();
        $write = new Stream(getcwd() . $nomeArquivo);
        $logger->addWriter($write);
        $logger->info($leads);

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(1_977_358_885);

        return $response;
    }
}
