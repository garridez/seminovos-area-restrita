<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestritaAnuncio\Controller;

use AreaRestrita\Controller\AbstractActionController;
use AreaRestrita\Model\Planos;
use Zend\View\Model\ViewModel;

class PlanoController extends AbstractActionController
{

    public function indexAction()
    {

        $dadosPlanos = $this->getContainer()
            ->get(Planos::class)
            ->get(2, true, -1);

        if ($this->getCadastro('tipoCadastro') == '1') {
            $template = 'revenda';
            $planosAnuncioRevenda = [
                5, # Básico
                2, # Turbo
                3, # Nitro
            ];
            $dadosPlanos = array_filter($dadosPlanos, function($i) use($planosAnuncioRevenda) {
                return in_array($i['idPlano'], $planosAnuncioRevenda);
            });
        } else {
            $template = 'particular';
            $dadosPlanos = array_filter($dadosPlanos, function($i) {
                return in_array($i['status'], [1]);
            });
        }

        $data = [
            'planos' => $dadosPlanos,
        ];

        $dadosVeiculo = $this->getVeiculo(5);

        if ($dadosVeiculo) {
            $data['prioridadePlano'] = (int) $dadosVeiculo['prioridadePlano'];
            $data['idPlanoAtual'] = (int) ($dadosVeiculo['idStatus'] == 1 || $dadosVeiculo['idStatus'] == 3 ? 0 : $dadosVeiculo['idPlano']);
            $data['idStatusAnuncio'] = (int) $dadosVeiculo['idStatus'];
            $data['zeroKm'] = (int) $dadosVeiculo['veiculo_zero_km'];
        }

        $viewModel = new ViewModel($data);

        $viewModel->setTerminal(true);

        return $viewModel;
    }
}
