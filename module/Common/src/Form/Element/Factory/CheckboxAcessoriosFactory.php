<?php

namespace SnBH\Common\Form\Element\Factory;

use SnBH\Common\Form\Element\CheckboxAcessorios;
use Interop\Container\ContainerInterface;
use SnBH\ApiModel\Model\VeiculoTipo;

class CheckboxAcessoriosFactory extends AbstractElementFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $checkboxAcessorios = new CheckboxAcessorios();

        $optionsVeiculoTipo = $this->getOptionsVeiculoTipo($container);

        $checkboxAcessorios->setOptionsVeiculoTipo($optionsVeiculoTipo);

        return $checkboxAcessorios;
    }

    protected function getOptionsVeiculoTipo($container)
    {
        $apiClient = $this->getApiClient($container);
        $idCaminhao = VeiculoTipo::getByName('caminhao');
        $acessoriosTipos = [
            $idCaminhao => []
        ];

        foreach (VeiculoTipo::$idTipos as $idTipo => $tipoNome) {
            // Caminhão não tem acessórios
            if ($idTipo == $idCaminhao) {
                continue;
            }

            $acessoriosData = $apiClient->acessorios([
                    'idTipo' => $idTipo
                    ], null, true)->getData();
            $acessorios = [];
            foreach ($acessoriosData as $ac) {
                $acessorios[$ac['idAcessorio']] = $ac['acessorio'];
            }
            $acessoriosTipos[$idTipo] = $acessorios;
        }
        return $acessoriosTipos;
    }
}
