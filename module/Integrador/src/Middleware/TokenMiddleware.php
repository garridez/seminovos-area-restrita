<?php

namespace SnBH\Integrador\Middleware;

use Psr\Http\Message\ServerRequestInterface as ServerRequestI;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface as DelegateI;
use Laminas\Diactoros\Response\JsonResponse;


class TokenMiddleware implements MiddlewareInterface
{

    public function __construct(protected $tokens)
    {
    }

    public function process(ServerRequestI $request, DelegateI $delegate)
    {
        $token = $request->getHeaderLine('X-SnBH-Token');
        $idCadastro = $request->getHeaderLine('X-SnBH-IdCadastro');

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

        // Tudo ok! Continua com a requisição
        return $delegate->process($request);
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
            'detail' => 'Nao autorizado'
            ], 401);
    }
}
