<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Service\CertificadoDocumental;
use AreaRestrita\Service\Identity;
use Laminas\Router\Http\RouteMatch;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use Throwable;

class CertificadosController extends AbstractActionController
{
    protected ContainerInterface $container;
    protected array $routeParams;

    public function __construct()
    {
        // phpcs:ignore
        global $container;
        $this->container = $container;

        /**
         * Apenas para mostrar na view a rota
         */
        /** @var RouteMatch $routeMatch */
        $routeMatch = $container
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->routeParams = $routeMatch->getParams();
        $this->routeParams['routeName'] = $routeMatch->getMatchedRouteName();
    }

    public function indexAction(): ViewModel
    {
        $placa = $this->params('placa');

        $res = $this->getApiClient()->veiculosCertificadosGet([], $placa);

        $dados = $res->getData();

        return new ViewModel([
            'placa' => $placa,
            'dadosCertificados' => $dados,
            // "Baixar PDF" (public/certificado-pdf/certificado-pdf.js).
            'certificadoPdf' => is_array($dados) ? $this->dadosPdf($dados, (string) $placa) : null,
            'baixarPdfAgora' => (bool) $this->params()->fromQuery('pdf', false),
        ]);
    }

    /**
     * Dados do PDF: só para o dono do anúncio (o anúncio vem no ?v= dos links de Meus veículos)
     * e com o certificado já processado.
     */
    private function dadosPdf(array $certificado, string $placa): ?array
    {
        $idVeiculo = (int) $this->params()->fromQuery('v', 0);
        if ($idVeiculo <= 0 || !$certificado || empty($certificado['dataCadastro'])) {
            return null;
        }
        try {
            /** @var Identity $identity */
            $identity = $this->getContainer()->get(Identity::class);
            $idCadastro = (int) $identity->getIdentity();
            $dadosVeiculo = $this->getApiClient()
                ->veiculosGet(['ignorarCondicoesBasicas' => 1], (string) $idVeiculo)
                ->getData();
        } catch (Throwable) {
            return null;
        }
        $v = is_array($dadosVeiculo) ? ($dadosVeiculo[0] ?? null) : null;
        if (
            !is_array($v)
            || $idCadastro <= 0
            || (int) ($v['idCadastro'] ?? 0) !== $idCadastro
            || !CertificadoDocumental::mesmaPlaca((string) ($v['placa'] ?? ''), $placa)
        ) {
            return null;
        }
        $placaPdf = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $placa));
        $pdf = CertificadoDocumental::paraPdf($certificado, $placaPdf, $v);

        return $pdf['pendente'] ? null : $pdf;
    }
}
