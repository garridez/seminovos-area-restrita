<?php

namespace AreaRestrita\Model;

use SnBH\ApiClient\Response;
use SnBH\ApiModel\Model\Cadastros as ApiModelCadastros;

class Cadastros extends ApiModelCadastros
{
    use Traits\TraitIdentity;

    /**
     * Retorna os dados do usuário logado
     *
     * @param bool $cacheable Determina se os dados vão vim do cache ou não
     * @return array|false
     */
    public function getCurrent($cacheable = true)
    {
        if ($this->getIdentity() === null) {
            return false;
        }
        return parent::get([
            'considerarInativo' => 1,
        ], $this->getIdentity(), $cacheable)->getData()[0];
    }

    /**
     * Atualiza na api os dados de cadastro
     * Se o parametro $idCadastro não for passado, será usado
     *  o $idCadastro da sessão
     *
     * @param int $idCadastro
     * @return Response
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
     *
     * @return boolean
     */
    public function isRevenda()
    {
        return $this->getCurrent()['tipoCadastro'] == self::TIPO_CADASTRO_REVENDA;
    }

    /**
     * @return Response
     */
    public function post(array $data)
    {
        return parent::post($data);
    }

    /**
     * @return array
     */
    public function get(array $data)
    {
        return parent::get($data)->getData();
    }
}
