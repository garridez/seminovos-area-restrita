<?php

namespace SnBH\ApiModel\Model;

class VeiculosStatus extends AbstractModel
{

    public const STATUS_AGUARDANDO_PAGAMENTO = 1;
    public const STATUS_ATIVO = 2;
    public const STATUS_CADASTRANDO = 3;
    public const STATUS_EXPIRADO = 4;
    public const STATUS_INATIVO = 5;
    public const STATUS_PENDENTE = 6;
    public const STATUS_REMOVIDO = 7;
    public const STATUS_VENDIDO = 8;
    public const STATUS_FOTOS_ALTERADAS = 9;
    public const STATUS_CADASTRANDO_GRATIS = 10;

}
