<?php

namespace SnBH\ApiModel\Model;

class VeiculosStatus extends AbstractModel
{

    final public const STATUS_AGUARDANDO_PAGAMENTO = 1;
    final public const STATUS_ATIVO = 2;
    final public const STATUS_CADASTRANDO = 3;
    final public const STATUS_EXPIRADO = 4;
    final public const STATUS_INATIVO = 5;
    final public const STATUS_PENDENTE = 6;
    final public const STATUS_REMOVIDO = 7;
    final public const STATUS_VENDIDO = 8;
    final public const STATUS_FOTOS_ALTERADAS = 9;
    final public const STATUS_CADASTRANDO_GRATIS = 10;

}
