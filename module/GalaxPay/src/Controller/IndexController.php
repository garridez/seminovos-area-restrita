<?php

namespace SnBH\GalaxPay\Controller;

use Laminas\View\Model\JsonModel;
use SnBH\Integrador\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $request = $this->request;
        if ($request->isPost()) {

            $data = json_decode((string) $request->getContent(), true);

            if ($data['confirmHash'] != WEBHOOK_GALAYPAY) {
                return new JsonModel([
                        'status' => 401,
                        'detail' => 'A chave enviada é inválida.'
                    ]);
            }

            $res = $this->getApiClient()->consultarPagamentoPost([
                'transaction' => $data['Transaction'],
                'metodo' => 'galaxpay'
                ])->json();

            return new JsonModel($res);
        }

        return new JsonModel([
            '405' => 'Parâmetros enviados inválidos.'
        ]);
    }
}
