<?php

namespace SnBH\ApiModel\Model;

class Cadastros extends AbstractModel
{

    public const TIPO_CADASTRO_REVENDA = 1;
    public const TIPO_CADASTRO_PARTICULAR = 2;
    public const TIPO_ID_STRING = [
        1 => 'revenda',
        2 => 'particular'
    ];
    public const TIPO_STRING_ID = [
        'revenda' => 1,
        'particular' => 2
    ];

}
