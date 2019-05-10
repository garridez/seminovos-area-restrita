<?php

namespace AreaRestrita\Controller;

use Zend\View\Model\JsonModel;
use Zend\Http\Header;

class FiltrosController extends AbstractActionController
{

    public function indexAction()
    {

        $data = $this->getApiClient()
            ->veiculosFiltrosGet(['motor' => 1], null, !true)
            ->getData();

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

        foreach ($data as &$tipos) {
            foreach ($tipos['marcas'] as &$marcas) {
                usort($marcas['modelos'], function ($a, $b) {
                    return strcmp($a['nome'], $b['nome']);
                });
            }
        }
        return new JsonModel($data);
    }
}
