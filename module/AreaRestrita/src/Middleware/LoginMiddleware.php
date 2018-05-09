<?php

namespace AreaRestrita\Middleware;

use Psr\Http\Message\ServerRequestInterface as ServerRequestI;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface as DelegateI;
use Psr\Http\Message\ResponseInterface as ResponseI;
use Interop\Container\ContainerInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Authentication\AuthenticationService;
use Zend\Router\Http\TreeRouteStack;

class LoginMiddleware implements MiddlewareInterface
{

    protected $authService;
    protected $router;

    public function __construct(AuthenticationService $authService, TreeRouteStack $router)
    {
        $this->authService = $authService;
        $this->router = $router;
    }

    public function process(ServerRequestI $request, DelegateI $delegate)
    {
        if ($this->authService->hasIdentity()) {
            // Executa o próximo middleware
            return $delegate->process($request);
        }

        $url = $this->router->getRoute('auth')->assemble();
        return new RedirectResponse($url);
    }
}
