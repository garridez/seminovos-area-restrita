<?php

namespace AreaRestrita\Controller;

use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

/**
 * Tela self-service de integrações da revenda.
 *
 * - Credenciais do Integrador (tabela integracao_token, via API /integracao-token):
 *   tokens usados por sistemas de gestão de estoque para publicar anúncios
 *   pelas rotas /integrador/* deste projeto.
 * - Feed Webmotors (tabela webmotors_cadastros, via API /webmotors-cadastros):
 *   token fornecido pela Webmotors para sincronização do estoque.
 *
 * REGRA DE OURO: o idCadastro vem SEMPRE da sessão (getCadastro),
 * NUNCA de formulário ou querystring. Assim um cliente jamais
 * enxerga ou altera tokens de outro.
 */
class IntegracoesController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->isRevenda()) {
            return $this->redirect()->toRoute('restrito');
        }

        $idCadastro = $this->getCadastro('idCadastro');

        // 3º parâmetro false = sem cache: dado sensível e mutável
        $tokens = $this->getApiClient()
            ->integracaoTokenGet([], $idCadastro, false)
            ->getData() ?? [];

        $webmotors = $this->getApiClient()
            ->webmotorsCadastrosGet([], $idCadastro, false)
            ->getData() ?? [];

        return new ViewModel([
            'tokens' => $tokens,
            'webmotors' => $webmotors[0] ?? null,
        ]);
    }

    public function gerarTokenAction(): JsonModel
    {
        if (!$this->isRevenda()) {
            return $this->naoAutorizado();
        }

        $nome = trim((string) $this->params()->fromPost('nome', ''));

        if ($nome === '') {
            $nome = 'integrador';
        }

        $res = $this->getApiClient()->integracaoTokenPost([
            'idCadastro' => $this->getCadastro('idCadastro'),
            'nome' => mb_substr($nome, 0, 50),
        ])->json();

        return new JsonModel($res);
    }

    public function excluirTokenAction(): JsonModel
    {
        if (!$this->isRevenda()) {
            return $this->naoAutorizado();
        }

        $idToken = (int) $this->params()->fromRoute('idToken');
        $idCadastro = $this->getCadastro('idCadastro');

        // Antes de excluir, confere que o token pertence à loja logada.
        // Sem isso, qualquer cliente poderia excluir token alheio por ID.
        $tokens = $this->getApiClient()
            ->integracaoTokenGet([], $idCadastro, false)
            ->getData() ?? [];

        $meusIds = array_map('intval', array_column($tokens, 'id_token'));

        if (!in_array($idToken, $meusIds, true)) {
            return new JsonModel([
                'status' => 403,
                'detail' => 'Token não encontrado neste cadastro.',
            ]);
        }

        $this->getApiClient()->integracaoTokenDelete([], $idToken);

        return new JsonModel(['status' => 200]);
    }

    public function salvarWebmotorsAction(): JsonModel
    {
        if (!$this->isRevenda()) {
            return $this->naoAutorizado();
        }

        $token = trim((string) $this->params()->fromPost('token', ''));

        if ($token === '') {
            return new JsonModel([
                'status' => 400,
                'detail' => 'Informe o token fornecido pela Webmotors.',
            ]);
        }

        $res = $this->getApiClient()->webmotorsCadastrosPost([
            'idCadastro' => $this->getCadastro('idCadastro'),
            'token' => $token,
        ])->json();

        return new JsonModel($res);
    }

    protected function naoAutorizado(): JsonModel
    {
        return new JsonModel([
            'status' => 403,
            'detail' => 'Disponível apenas para revendas.',
        ]);
    }
}
