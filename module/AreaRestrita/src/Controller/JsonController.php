<?php

namespace AreaRestrita\Controller;

use Laminas\Http\Header;
use Laminas\View\Model\JsonModel;

class JsonController extends AbstractActionController
{

    public function cidadesAction()
    {
        $params = $this->params()->fromQuery();
        unset($params['idEstado']);

        $cidadesData = $this->getApiClient()->cidadesGet($params, null, true)->getData();
        $this->setHeaderCache();

        $data = [];
        foreach ($cidadesData as $cidade) {
            $idEstado = $cidade['idEstado'];
            unset($cidade['DDD'],
                $cidade['dDD'],
                $cidade['sigla'],
                $cidade['idEstado']);

            $data[$idEstado][] = $cidade;
        }
        $params = $this->params()->fromQuery();
        if (isset($params['idEstado']) && isset($data[$params['idEstado']])) {
            $data = $data[$params['idEstado']];
            if ($params['idEstado'] == 11) {
                array_unshift($data,
                    [
                        'idCidade' => '2700',
                        'cidade' => 'Belo Horizonte',
                    ],
                    [
                        'idCidade' => '2922',
                        'cidade' => 'Contagem',
                    ],
                    [
                        'idCidade' => '2707',
                        'cidade' => 'Betim',
                    ],
                    [
                        'idCidade' => '',
                        'cidade' => '-',
                ]);
            }
        }


        return new JsonModel($data);
    }

    protected function setHeaderCache()
    {

        $expires = new Header\Expires();
        $expires->setDate(date(DATE_W3C, time() + (60 * 60 * 24 * 31)));

        $cacheControl = new Header\CacheControl();
        $cacheControl->addDirective('max-age', 2_592_000);

        $pragma = new Header\Pragma('cache');

        /* @var $response \Laminas\Http\PhpEnvironment\Response */
        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeader($expires)
            ->addHeader($pragma)
            ->addHeader($cacheControl);
    }
}
