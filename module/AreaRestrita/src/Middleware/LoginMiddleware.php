<?php

namespace AreaRestrita\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Interop\Http\ServerMiddleware\DelegateInterface as DelegateI;
use Psr\Http\Message\ResponseInterface as ResponseI;
use Interop\Container\ContainerInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Authentication\AuthenticationService;
use Laminas\Router\Http\TreeRouteStack;

class LoginMiddleware implements MiddlewareInterface
{

    protected $authService;
    protected $router;

    public function __construct(AuthenticationService $authService, TreeRouteStack $router)
    {
        $this->authService = $authService;
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        if ($this->authService->hasIdentity()) {
            // Executa o próximo middleware
            return $delegate->handle($request);
        }

        $url = $this->router->getRoute('auth')->assemble();
        return new RedirectResponse($url);
    }
}
