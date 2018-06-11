<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\HistoricoPagamentosParticular as ApiModelPagamentos;

class Pagamentos extends ApiModelPagamentos
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
    public function put(array $data, $idCadastro = null)
    {
        if ($idCadastro === null) {
            $idCadastro = $this->getIdentity();
        }

        return parent::put($data, $idCadastro);
    }

    public function get($idPagamento = null)
    {
        return parent::get([
            'idCadastro' => $this->getIdentity(),
            'sort' => 'idPagamento',
            'direction' => 'DESC'
        ], $idPagamento)->json();

    }
}
