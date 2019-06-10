<?php

namespace AreaRestrita\Controller;

use Zend\Http\Header;
use Zend\View\Model\JsonModel;

class JsonController extends AbstractActionController
{

    public function cidadesAction()
    {
        $params = $this->params()->fromQuery();

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
        return new JsonModel($data);
    }

    protected function setHeaderCache()
    {

        $expires = new Header\Expires();
        $expires->setDate(date(DATE_W3C, time() + (60 * 60 * 24 * 31)));

        $cacheControl = new Header\CacheControl();
        $cacheControl->addDirective('max-age', 2592000);

        $pragma = new Header\Pragma('cache');

        /* @var $response \Zend\Http\PhpEnvironment\Response */
        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeader($expires)
            ->addHeader($pragma)
            ->addHeader($cacheControl);
    }
}
