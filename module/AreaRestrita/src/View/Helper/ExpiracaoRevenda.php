<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Esse helper é pra mostrar os dados de quantidade de anúncios usados e disponiveis na view
 *
 * @property date $dataExpiracaoRevenda Total de Gratuitos
 * @property int $diasParaExpirar Total de Gratuito Publicado
 */
class ExpiracaoRevenda extends AbstractHelper
{
    public function __construct(protected mixed $data)
    {
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
