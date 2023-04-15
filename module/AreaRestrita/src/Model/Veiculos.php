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
     * @param int $idCadastro
     * @return \SnBH\ApiClient\Response
     */
    public function put(array $data, $idVeiculo)
    {
        return parent::put($data, $idVeiculo)->json();
    }

    public function getAll($page = null, $cache = false)
    {
        return parent::get([
                'idCadastro' => $this->getIdentity(),
                'ignorarCondicoesBasicas' => true,
                'registrosPagina' => 100,
                'paginaAtual' => $page,
                'ordenarPor' => 5, // status
                'ordem' => 'ASC',
                'cache' => $cache ? 1 : 0,
                ], null, $cache)->json();
    }

    public function get($idVeiculo, $useCache = false)
    {
        $res = parent::get(['ignorarCondicoesBasicas' => true], $idVeiculo, $useCache);
        if ($res->status == 200) {
            return $res->getData()[0];
        }
        return false;
    }

    public function delete($idVeiculo)
    {
        return parent::delete(null, $idVeiculo)->json();
    }

    public function post(array $data)
    {
        return parent::post($data)->getData();
    }
}
