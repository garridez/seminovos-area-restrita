<?php

namespace SnBH\ApiModel\Model;

class VeiculosInfo extends AbstractModel
{
    public function get(string|int $idModelo, string|int $anoDe, string|int $anoAte): array
    {
        return $this->apiClient->veiculosInfoGet([
            'modelo' => $idModelo,
            'anoDe' => $anoDe,
            'anoAte' => $anoAte,
        ], null, 60 * 60 * 24)->getData();
    }
}
