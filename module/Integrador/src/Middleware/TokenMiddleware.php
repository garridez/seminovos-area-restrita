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
    public function __construct(protected $tokens)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var ServerRequest $request */

        $token = $request->getHeaderLine('X-SnBH-Token');
        $idCadastro = (int) $request->getHeaderLine('X-SnBH-IdCadastro');

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
        if ($tokenData['idCadastro'] !== $idCadastro && !$acessoToken) {
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
     * @param string $token
     */
    public function getTokenData($token): bool|array
    {
        foreach ($this->tokens as $tokenData) {
            if ($tokenData['token'] === $token) {
                return $tokenData;
            }
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
