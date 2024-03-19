<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Esse helper é pra mostrar os dados de quantidade de anúncios usados e disponiveis na view
 *
 * @property int $totalGratuito Total de Gratuitos
 * @property int $totalGratuitoPublicado Total de Gratuito Publicado
 * @property int $totalBasico Total de Basicos
 * @property int $totalBasicoPublicado Total de Basico Publicado
 * @property int $totalTurbo Total de Turbo
 * @property int $totalTurboPublicados Total de Turbo Publicados
 * @property int $totalNitro Total de Nitro
 * @property int $totalNitroPublicados Total de Nitro Publicados
 * @property int $totalNitroHome Total de Nitro Home
 * @property int $totalNitroHomePublicados Total de NitroHome Publicados
 * @property int $totalAnuncios Total de Anuncios
 * @property int $totalAnunciosPublicados Total de Anuncios Publicados
 */
class QtdAnuncios extends AbstractHelper
{
    public function __construct(protected ?array $data)
    {
    }

    public function __invoke()
    {
        return $this;
    }

    /**
     * @param string $name
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function getData(): array
    {
        return $this->data ?? [];
    }
}
