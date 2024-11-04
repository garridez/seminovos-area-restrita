<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Model\Planos;
use Laminas\View\Model\ViewModel;

class PlanoController extends AbstractActionController
{
    public function indexAction()
    {
        $dadosPlanos = $this->getContainer()
            ->get(Planos::class)
            ->get(2, true, -1);

        if ($this->getCadastro('tipoCadastro') == '1') {
            $planosAnuncioRevenda = [
                5, // Básico
                2, // Turbo
                3, // Nitro
            ];
            $dadosPlanos = array_filter($dadosPlanos, function ($i) use ($planosAnuncioRevenda): bool {
                return in_array($i['idPlano'], $planosAnuncioRevenda);
            });
        } else {
            $dadosPlanos = array_filter($dadosPlanos, function ($i): bool {
                return $i['status'] == 1;
            });
        }

        $data = [
            'planos' => $dadosPlanos,
            'planoAtualDados' => [],
        ];

        $dadosVeiculo = $this->getVeiculo(5);
        $data['dadosVeiculo'] = $dadosVeiculo;

        if ($dadosVeiculo) {
            $data['prioridadePlano'] = (int) $dadosVeiculo['prioridadePlano'];
            $data['idPlanoAtual'] = (int) ($dadosVeiculo['idStatus'] == 1 || $dadosVeiculo['idStatus'] == 3 ? 0 : $dadosVeiculo['idPlano']);
            $data['idStatusAnuncio'] = (int) $dadosVeiculo['idStatus'];
            $data['zeroKm'] = (int) $dadosVeiculo['veiculo_zero_km'];
            foreach($dadosPlanos as $plano) {
                if ($plano['idPlano'] == $data['idPlanoAtual']) {
                    $data['planoAtualDados'] = $plano;
                    break;
                }

            }
        }

        $viewModel = new ViewModel($data);

        $viewModel->setTerminal(true);

        return $viewModel;
    }
}
