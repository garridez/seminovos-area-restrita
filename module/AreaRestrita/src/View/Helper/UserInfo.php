<?php

namespace AreaRestrita\View\Helper;

use SnBH\ApiModel\Model\Cadastros;
use Zend\View\Helper\AbstractHelper;

class UserInfo extends AbstractHelper
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __invoke($key = null)
    {
        $d = $this->data;
        return $key !== null ? $d[$key] : $this;
    }

    /**
     * Retorna um array com todos os dados
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @todo Criar uma opção na API para salvar imagem de perfil
     * @return string
     */
    public function getImgPerfil()
    {
        if (isset($this->data['imgPerfil'])) {
            return $this->data['imgPerfil'];
        }
        preg_match('/(?P<inicial>^.).*?\s(?P<segunda>.)/', $this->data['responsavelNome'], $matches);

        return '<span>' . strtoupper($matches['inicial'] . $matches['segunda']) . '</span>';
    }

    /**
     * Retorna true se o tipo de cadastro do usuário logado é revenda
     * @return bool
     */
    public function isRevenda(): bool
    {
        return $this('tipoCadastro') == Cadastros::TIPO_CADASTRO_REVENDA;
    }

    /**
     * Retorna true se o tipo de cadastro do usuário logado é particular
     * @return bool
     */
    public function isParticular(): bool
    {
        return $this('tipoCadastro') == Cadastros::TIPO_CADASTRO_PARTICULAR;
    }
}
