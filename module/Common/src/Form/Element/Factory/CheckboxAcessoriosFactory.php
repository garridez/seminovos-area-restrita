<?php

namespace SnBH\Common\Form\Element\Factory;

use Psr\Container\ContainerInterface;
use SnBH\ApiModel\Model\VeiculoTipo;
use SnBH\Common\Form\Element\CheckboxAcessorios;

class CheckboxAcessoriosFactory extends AbstractElementFactory
{
    /**
     * @param string             $requestedName
     * @param array|null         $options
     * @return CheckboxAcessorios
     */
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $checkboxAcessorios = new CheckboxAcessorios();

        $optionsVeiculoTipo = $this->getOptionsVeiculoTipo($container);

        $checkboxAcessorios->setOptionsVeiculoTipo($optionsVeiculoTipo);

        return $checkboxAcessorios;
    }

    protected function getOptionsVeiculoTipo(ContainerInterface $container): array
    {
        $apiClient = $this->getApiClient($container);
        $idCaminhao = VeiculoTipo::getByName('caminhao');
        $acessoriosTipos = [
            $idCaminhao => [],
        ];

        $cambios = [11, 60, 78];

        foreach (VeiculoTipo::$idTipos as $idTipo => $tipoNome) {
            // Caminhão não tem acessórios
            if ($idTipo == $idCaminhao) {
                continue;
            }

            $acessoriosData = $apiClient->acessorios([
                'idTipo' => $idTipo,
            ], null, true)->getData();
            $acessorios = [];
            foreach ($acessoriosData as $ac) {
                if (!in_array($ac['idAcessorio'], $cambios)) {
                    $acessorios[$ac['idAcessorio']] = $ac['acessorio'];
                }
            }
            $acessoriosTipos[$idTipo] = $acessorios;
        }
        return $acessoriosTipos;
    }
}
