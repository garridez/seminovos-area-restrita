<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\Cadastros as ApiModelCadastros;

class Cadastros extends ApiModelCadastros
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
        if ($this->getIdentity() === null) {
            return false;
        }
        return parent::get([
                'considerarInativo' => 1
                ], $this->getIdentity(), $cacheable)->getData()[0];
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

    /**
     * Atalho para verificar se o usuario atual é revenda ou não
     * @return boolean
     */
    public function isRevenda()
    {
        return $this->getCurrent()['tipoCadastro'] == self::TIPO_CADASTRO_REVENDA;
    }

    public function post(array $data)
    {
        return parent::post($data);
    }

    public function get(array $data)
    {
        return parent::get($data)->getData();
    }
}
