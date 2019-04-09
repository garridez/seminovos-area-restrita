<?php

namespace SnBH\ApiModel\Model;

class Cadastros extends AbstractModel
{

    const TIPO_CADASTRO_REVENDA = 1;
    const TIPO_CADASTRO_PARTICULAR = 2;
    const TIPO_ID_STRING = [
        1 => 'revenda',
        2 => 'particular'
    ];
    const TIPO_STRING_ID = [
        'revenda' => 1,
        'particular' => 2
    ];

}
