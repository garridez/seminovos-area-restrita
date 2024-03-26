<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\SiteHospedadoBanner as ApiModelSiteHospedadoBanner;

class SiteHospedadoBanner extends ApiModelSiteHospedadoBanner
{
    use Traits\TraitIdentity;

    /**
     * Retorna os dados do usuário logado
     *
     * @param bool $cacheable Determina se os dados vão vim do cache ou não
     * @return array
     */
    public function getCurrent($cacheable = true)
    {
        return parent::get([], $this->getIdentity(), $cacheable)->getData()[0];
    }

    /**
     * @param int $idSiteHospedado
     * @return array
     */
    public function get($idSiteHospedado)
    {
        return parent::get([
            'idSiteHospedado' => $idSiteHospedado,
        ])->getData();
    }
}
