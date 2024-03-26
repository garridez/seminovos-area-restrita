<?php

namespace AreaRestrita\Middleware;

use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Router\Http\TreeRouteStack;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginMiddleware implements MiddlewareInterface
{
    public function __construct(protected AuthenticationService $authService, protected TreeRouteStack $router)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->authService->hasIdentity()) {
            // Executa o próximo middleware
            return $handler->handle($request);
        }

        $url = $this->router->getRoute('auth')->assemble();
        return new RedirectResponse($url);
    }
}
