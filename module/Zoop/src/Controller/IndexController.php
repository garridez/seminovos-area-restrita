<?php

namespace SnBH\Zoop\Controller;

use Zend\View\Model\JsonModel;
use SnBH\Integrador\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->request;
        if ($request->isPost()) {
            file_put_contents('data/log/zoop/' . date('dmY') . '.log', date('Y-m-d h:i:s') . "\n" . $request->getContent() . "\n\n", FILE_APPEND);
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['payload']['object'])) {
                return new JsonModel([
                        'status' => 401,
                        'detail' => 'Formato invalido invalido.'
                    ]);
            }
            
            if (isset($data['payload']['object']['ping']) && $data['payload']['object']['ping']) {
                return new JsonModel([
                        'status' => 200,
                        'detail' => 'Ping efetuado.'
                    ]);
            }

            $res = $this->getApiClient()->consultarPagamentoPost([
                'transaction' => $data['payload']['object'],
                'metodo' => 'zoop'
                ])->json();
                  
            
            return new JsonModel($res);
        }
        
        return new JsonModel([
            '405' => 'Parâmetros enviados inválidos.'
        ]);
    }
}
