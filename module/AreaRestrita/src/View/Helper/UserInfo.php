<?php

namespace AreaRestrita\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use SnBH\ApiModel\Model\Cadastros;

class UserInfo extends AbstractHelper
{
    public function __construct(protected $data)
    {
    }

    public function __invoke($key = null)
    {
        if ($key === null) {
            return $this;
        }
        $d = $this->data;
        return $d[$key] ?? false;
    }

    /**
     * Retorna um array com todos os dados
     *
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
        preg_match('/(?P<inicial>^.).*?\s(?P<segunda>.)/', (string) $this->data['responsavelNome'], $matches);

        return '<span>' . strtoupper($matches['inicial'] . $matches['segunda']) . '</span>';
    }

    /**
     * Retorna true se o tipo de cadastro do usuário logado é revenda
     */
    public function isRevenda(): bool
    {
        return $this('tipoCadastro') == Cadastros::TIPO_CADASTRO_REVENDA;
    }

    /**
     * Retorna true se o tipo de cadastro do usuário logado é particular
     */
    public function isParticular(): bool
    {
        return $this('tipoCadastro') == Cadastros::TIPO_CADASTRO_PARTICULAR;
    }
}
