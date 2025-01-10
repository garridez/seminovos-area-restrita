<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\VeiculosFotos as ApiModelVeiculosFotos;

class VeiculosFotos extends ApiModelVeiculosFotos
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
     * @param int $idVeiculo
     * @return array|false
     */
    public function get($idVeiculo)
    {
        $res = parent::get([
            'idVeiculo' => $idVeiculo,
        ]);
        if ($res->status === 404){
            return false;
        }
        return $res->getData();
    }

    /**
     * @param array $dados
     * @return array
     */
    public function delete($dados)
    {
        return parent::delete($dados)->json();
    }
}
