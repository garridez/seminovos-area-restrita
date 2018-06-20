<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\ServicosAdicionais as ApiModelServicosAdicionais;

class ServicosAdicionais extends ApiModelServicosAdicionais
{

    use Traits\TraitIdentity;

    /**
     * Retorna os dados do usuário logado
     * 
     * @param bool $cacheable Determina se os dados vão vim do cache ou não
     * @return array
     */
    public function getCurrent($cacheable = false)
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
    public function get($idServicosAdicional)
    {
        return parent::get([
                'idServicosAdicional' => $idServicosAdicional
            ])->json();
    }
}
