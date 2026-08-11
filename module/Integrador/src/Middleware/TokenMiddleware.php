<?php

namespace SnBH\Integrador\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TokenMiddleware implements MiddlewareInterface
{
    /**
     * @param array $tokens Lista de tokens (normalmente vinda do cache)
     * @param callable|null $refreshTokens Callback que retorna a lista de tokens
     *                                     atualizada (sem cache). Usado como
     *                                     fallback quando o token não é
     *                                     encontrado na lista cacheada.
     */
    public function __construct(protected $tokens, protected $refreshTokens = null) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $phpsessid = $request->getHeaderLine('X-SnBH-PHPSESSID');
        $idCadastro = (int) $request->getHeaderLine('X-SnBH-IdCadastro');

        if ($phpsessid) {
            session_id($phpsessid);
            session_start();
            if (
                $_SESSION
                && isset($_SESSION['app_login'])
                && isset($_SESSION['idCadastro'])
                && $_SESSION['idCadastro'] == $idCadastro
            ) {
                return $handler->handle($request);
            }
        }

        /** @var ServerRequest $request */

        $token = $request->getHeaderLine('X-SnBH-Token');

        $acessoToken = false;
        if ($request->getHeaderLine('X-SnBH-Cadastro')) {
            $acessoToken = true;
            $idCadastro = true;
        }

        // Os dois campos são obrigatórios
        if (!$token || !$idCadastro) {
            return $this->naoAutorizadoResponse();
        }

        $tokenData = $this->getTokenData($token);

        if (!$tokenData) {
            return $this->naoAutorizadoResponse();
        }

        // O idCadastro passado deve ser igual ao idCadastro que está no banco
        // (cast para int pois o valor pode chegar como string do JSON da API)
        if (!$acessoToken && (int) $tokenData['idCadastro'] !== $idCadastro) {
            return $this->naoAutorizadoResponse();
        }

        if (str_starts_with($request->getUri()->getPath(), '/integrador/revendas')) {
            if ($tokenData['token'] !== '879db53b62e90337D13316e85e81FaBe6f4943722090B568d6') {
                return $this->naoAutorizadoResponse();
            }
        }

        // Tudo ok! Continua com a requisição
        return $handler->handle($request);
    }

    /**
     * Retorna os dados do token
     *
     * Se o token não estiver na lista (que normalmente vem de cache),
     * busca a lista atualizada sem cache uma única vez antes de negar.
     * Isso cobre tokens recém-criados — ex.: loja reativada que refaz o
     * login do integrador e recebe um token novo, ainda fora do cache.
     *
     * @param string $token
     */
    public function getTokenData($token): bool|array
    {
        foreach ($this->tokens as $tokenData) {
            if ($tokenData['token'] === $token) {
                return $tokenData;
            }
        }

        if ($this->refreshTokens !== null) {
            $this->tokens = (array) ($this->refreshTokens)();
            // Evita nova busca na mesma requisição
            $this->refreshTokens = null;

            return $this->getTokenData($token);
        }

        return false;
    }

    public function naoAutorizadoResponse()
    {
        return new JsonResponse([
            'status' => 401,
            'detail' => 'Nao autorizado',
        ], 401);
    }
}
