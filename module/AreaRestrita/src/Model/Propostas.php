<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\Veiculos as ApiModelVeiculos;

class Propostas extends ApiModelVeiculos
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
     * Retorna todas as propostas
     * A chave do array é idAnuncio, porém deverá ser passado o idVeiculo
     * @param array $data
     * @param int $idVeiculo
     * @param string idProposta - campo que será realizado a ordenação dos resultados
     * @param string DESC - campo que será realizado para determinar o tipo de ordenação
     * @return \SnBH\ApiClient\Response
     */
    public function getAll($idVeiculo)
    {
        return parent::get([
                'idAnuncio' => $idVeiculo,
                'sort' => 'idProposta', /* TODO - falta implementar na API */
                'direction' => 'DESC'
            ])->getData();
    }
}
