<?php

namespace AreaRestrita\Model;

use SnBH\ApiModel\Model\CadastroRecorrencia as ApiModelCadastroRecorrencia;
use Throwable;

class CadastroRecorrencia extends ApiModelCadastroRecorrencia
{
    use Traits\TraitIdentity;

    /**
     * Retorna a recorrência ativa do usuário logado (ou null).
     *
     * Formato retornado pela API (/cadastro-recorrencia?idCadastro=X):
     * [
     *   'idCadastroRecorrencia' => int,
     *   'idCadastro' => int,
     *   'externalId' => string,
     *   'status' => int,
     *   'valor' => float,
     *   'created_at' => string|null,
     *   'card' => [
     *      'bandeira' => 'MasterCard',
     *      'bandeiraId' => 'mastercard',
     *      'numeroMascarado' => '222763******7232',
     *      'final' => '7232',
     *      'validade' => '2033-09',
     *   ],
     * ]
     *
     * @return array|null
     */
    public function getAtiva()
    {
        try {
            $response = parent::get([
                'idCadastro' => $this->getIdentity(),
            ], null, false)->json();

            if (($response['status'] ?? null) != 200) {
                return null;
            }

            return $response['data'][0] ?? null;
        } catch (Throwable $e) {
            // A página Financeiro nunca deve quebrar por falha na consulta da recorrência
            return null;
        }
    }
}
