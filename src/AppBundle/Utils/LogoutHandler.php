<?php

namespace AppBundle\Utils;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class LogoutHandler implements LogoutSuccessHandlerInterface
{
    protected $router;
    protected $utils;
    protected $target_url;

    public function __construct(Router $router, HttpUtils $utils, $target_url)
    {
        $this->router = $router;
        $this->utils = $utils;
        $this->target_url = $router->generate('admin_login', ['from_logout' => true], 0);
    }
    public function onLogoutSuccess(Request $request)
    {
        $this->router->generate('admin_index');


        return $this->utils->createRedirectResponse($request, $this->target_url);
    }
}