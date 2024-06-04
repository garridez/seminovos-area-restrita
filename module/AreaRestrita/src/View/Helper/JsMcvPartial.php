<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class JsMcvPartial extends AbstractHelper
{
    public function __construct(protected array $data)
    {
    }

    public function __invoke(): self
    {
        return $this;
    }

    public function __toString(): string
    {
        if (!$this->data) {
            return '';
        }
        $mixManifest = json_decode(file_get_contents('public/mix-manifest.json'), true);
        $seletor = '.' . $this->data['controller'] . '.' . $this->data['action'];
        $filesKeys = array_filter(array_keys($mixManifest), fn ($key) => str_contains($key, $seletor));

        $html = '';
        $idDev = APPLICATION_ENV === 'development';

        foreach ($filesKeys as $key) {
            $assetSufix = $idDev ? filemtime('public/' . $mixManifest[$key]) : APPLICATION_VERSION;
            $html .= '<script src="' . $mixManifest[$key] . '?' . $assetSufix . '" async></script>';
        }
        return $html;
    }
}
