<?php

namespace SnBH\Zoop\Controller;

use Laminas\View\Model\JsonModel;
use SnBH\Integrador\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        /* @var $request \Laminas\Http\PhpEnvironment\Request */
        $request = $this->request;
        if ($request->isPost()) {
            file_put_contents('data/logs/zoop/' . date('dmY') . '.log', date('Y-m-d h:i:s') . "\n" . $request->getContent() . "\n\n", FILE_APPEND);
            $data = json_decode((string) $request->getContent(), true);
            
            if (!isset($data['payload']['object'])) {
                \SnBH\Common\Logs\Zoop::fail($data);
                return new JsonModel([
                        'status' => 401,
                        'detail' => 'Formato invalido invalido.'
                    ]);
            }
            
            if (isset($data['payload']['object']['ping']) && $data['payload']['object']['ping']) {
                \SnBH\Common\Logs\Zoop::ping();
                return new JsonModel([
                        'status' => 200,
                        'detail' => 'Ping efetuado.'
                    ]);
            }

            \SnBH\Common\Logs\Zoop::ok($data);
            $res = $this->getApiClient()->consultarPagamentoPost([
                'transaction' => $data['payload']['object'],
                'metodo' => 'zoop'
                ])->json();
                  
            
            return new JsonModel($res);
        }
        
        \SnBH\Common\Logs\Zoop::notPost();
        return new JsonModel([
            '405' => 'Parâmetros enviados inválidos.'
        ]);
    }
}
