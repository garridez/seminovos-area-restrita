<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\SiteHospedado as ApiModelSiteHospedado;

class SiteHospedado extends ApiModelSiteHospedado
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
     * Atualiza na api os dados de cadastro
     * Se o parametro $idCadastro não for passado, será usado
     *  o $idCadastro da sessão
     * @param array $data
     * @param int $idCadastro
     * @return \SnBH\ApiClient\Response
     */
    public function get()
    {
        return parent::get([
                'idCadastro' => $this->getIdentity()
            ])->json();
    }
}
