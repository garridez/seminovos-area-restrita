<?php

namespace SnBH\Common;

use AreaRestrita\Model\Traits\TraitIdentity;
use AreaRestrita\Model\Veiculos;

class ServiceVeiculo
{
    use TraitIdentity;

    /*
     * Verifica se o veiculo consultado pertence ao usuário que está logado
     */

    public function verificaCadastroVeiculo($idVeiculo)
    {
        global $container;

        /** @var Veiculos $veiculosModel */
        $veiculosModel = $container->get(Veiculos::class);

        // Busca os dados do cadastro
        $dadosVeiculo = $veiculosModel->get($idVeiculo, true);

        return $dadosVeiculo['cadastro']['idCadastro'] == $this->getIdentity();
    }
}
