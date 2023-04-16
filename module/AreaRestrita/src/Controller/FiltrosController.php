<?php

namespace AreaRestrita\Controller;

use Laminas\View\Model\JsonModel;
use Laminas\Http\Header;

class FiltrosController extends AbstractActionController
{

    public function indexAction()
    {

        $data = $this->getApiClient()
            ->veiculosFiltrosGet(['motor' => 1], null, true)
            ->getData();

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

        foreach ($data as &$tipos) {
            foreach ($tipos['marcas'] as &$marcas) {
                usort($marcas['modelos'], function ($a, $b): int {
                    return strcmp((string) $a['nome'], (string) $b['nome']);
                });
            }
        }
        return new JsonModel($data);
    }
}
