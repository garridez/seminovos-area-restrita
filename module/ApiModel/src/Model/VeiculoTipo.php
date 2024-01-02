<?php

namespace SnBH\ApiModel\Model;

class VeiculoTipo
{
    final public const TIPO_CARRO = 1;
    final public const TIPO_CAMINHAO = 2;
    final public const TIPO_MOTO = 3;

    public static array $idTipos = [
        1 => 'carro',
        2 => 'caminhão',
        3 => 'moto'
    ];

    public static array $tipos = [
        'carro' => 1,
        'caminhão' => 2,
        'caminhao' => 2,
        'moto' => 3
    ];

    /**
     * Retorna o ID do tipo pelo nome
     * @param string $name
     * @return string|false
     */
    public static function getByName($name)
    {
        return self::$tipos[strtolower($name)] ?? false;
    }

    /**
     * Retorna o nome do tipo pelo ID
     * @param int $id
     * @return string|false
     */
    public static function getById($id)
    {
        return self::$idTipos[(int) $id] ?? false;
    }

    /**
     * Essa função detect se é ID ou nome e sempre retorna o ID
     * @return int
     */
    public static function getIdByAny(int|string $var)
    {
        $varInt = (int) $var;
        if (((string) $varInt) == $var) {
            return $var;
        }
        return self::getByName($var);
    }
}
