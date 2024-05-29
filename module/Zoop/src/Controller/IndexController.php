<?php

namespace SnBH\Zoop\Controller;

use Laminas\View\Model\JsonModel;
use SnBH\Common\Logs\Zoop;
use SnBH\Integrador\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $request = $this->request;
        if ($request->isPost()) {
            file_put_contents('data/logs/zoop/' . date('dmY') . '.log', date('Y-m-d h:i:s') . "\n" . $request->getContent() . "\n\n", FILE_APPEND);
            $data = json_decode((string) $request->getContent(), true);

            if (!isset($data['payload']['object'])) {
                Zoop::fail($data);
                return new JsonModel([
                    'status' => 401,
                    'detail' => 'Formato invalido invalido.',
                ]);
            }

            if (isset($data['payload']['object']['ping']) && $data['payload']['object']['ping']) {
                Zoop::ping();
                return new JsonModel([
                    'status' => 200,
                    'detail' => 'Ping efetuado.',
                ]);
            }

            Zoop::ok($data);
            $res = $this->getApiClient()->consultarPagamentoPost([
                'transaction' => $data['payload']['object'],
                'metodo' => 'zoop',
            ])->json();

            return new JsonModel($res);
        }

        Zoop::notPost();
        return new JsonModel([
            'status' => '405',
            'detail' => 'Parâmetros enviados inválidos.',
        ]);
    }
}
