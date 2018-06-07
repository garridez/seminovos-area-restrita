<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\Veiculos as ApiModelVeiculos;

class Veiculos extends ApiModelVeiculos
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
     * Atualiza na api os dados do veiculo
     * Se o parametro $idCadastro não for passado, será usado
     *  o $idCadastro da sessão
     * @param array $data
     * @param int $idCadastro
     * @return \SnBH\ApiClient\Response
     */
    public function put(array $data, $idVeiculo)
    {
        return parent::put($data, $idVeiculo)->json();
    }

    public function get()
    {
        return parent::get([
            'idCadastro' => $this->getIdentity(),
            'ignorarCondicoesBasicas' => true
        ])->json();

    }

    public function getVeiculo($dados)
    {
        return parent::get($dados)->json();

    }

    public function delete($idVeiculo)
    {
        return parent::delete(null , $idVeiculo)->json();

    }
}
