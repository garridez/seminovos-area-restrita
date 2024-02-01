<?php

namespace AreaRestrita\View\Helper\Factory;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Model\Pagamentos;
use AreaRestrita\View\Helper\ExpiracaoRevenda;
use Interop\Container\ContainerInterface;
use SnBH\ApiClient\Client as ApiClient;
use Laminas\Authentication\AuthenticationService as AuthService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ExpiracaoRevendaFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $idCadastro = $container->get(AuthService::class)->getIdentity();

        $tipoCadastro = $container->get(Cadastros::class)->getCurrent(true)['tipoCadastro'];

        //pegar data de expiração do ultimo pagamento da revenda
        /* @var $pagamentosModel Pagamentos */
        $pagamentosModel = $container->get(Pagamentos::class);
        // Busca os dados do pagamento
        $pagamentosVeiculos = $pagamentosModel->get(null, true);
        //var_dump($pagamentosVeiculos);
        $dataAtual = new \DateTime(date('Y-m-d'));

        $dataExpiracaoPlano = null;
        $dataExpiracaoPlano = $this->getVariavelltimoPagamentoCadastro($pagamentosVeiculos, $idCadastro, "dataExpiracao");
        $dataExpiracao = new \DateTime($dataExpiracaoPlano ?: '');
        $intevaloData = $dataAtual->diff($dataExpiracao);
        $intevaloData = (int) ($intevaloData->format('%R%a') === '-0' ? -1 : $intevaloData->format('%R%a'));
        $dataExpiracao = $dataExpiracao->format('d/m/Y');

        $data = ['diasParaExpirar' => $intevaloData, 'dataExpiracaoRevenda' => $dataExpiracao];

        return new ExpiracaoRevenda($data);
    }

    /*
     * Verifica qual a ultima entrada de pagamento e captura a variavel solicitada desse
     * @param array $pagamentosVeiculos, int $idCadastro, string $variavel
     * @return type $result
     */
    protected function getVariavelltimoPagamentoCadastro($pagamentosVeiculos, $idCadastro, $variavel)
    {
        if (!isset($pagamentosVeiculos['data'])) {
            return null;
        }

        $result = null;
        $auxData = null; //new \DateTime('1969-01-01');
        $auxDataExpiracao = null;

        foreach ($pagamentosVeiculos['data'] AS $pagamento) {
            if ($pagamento["idCadastro"] == $idCadastro && $pagamento["status"] == 2) {
                $dataCadastro = new \DateTime($pagamento["dataCadastro"]);
                $dataExpiracao = new \DateTime($pagamento["dataExpiracao"]);

                if (($dataCadastro > $auxData) || ($dataExpiracao > $auxDataExpiracao)) {
                    $auxData = $dataCadastro;
                    $auxDataExpiracao = $dataExpiracao;
                    $result = $pagamento[$variavel];
                }
            }
        }

        return $result;
    }

}
