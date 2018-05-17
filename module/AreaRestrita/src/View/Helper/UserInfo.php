<?php

namespace AreaRestrita\View\Helper;

use Zend\View\Helper\AbstractHelper;
use SnBH\ApiModel\Model\Cadastros;

// This view helper class displays a menu bar.
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
//        ob_clean();

        if (isset($this->data['imgPerfil'])) {
            return $this->data['imgPerfil'];
        }
        preg_match('/(?P<inicial>^.).*?\s(?P<segunda>.)/', $this->data['responsavelNome'], $matches);

        return '<span>' . strtoupper($matches['inicial'] . $matches['segunda']) . '</span>';
    }
}
