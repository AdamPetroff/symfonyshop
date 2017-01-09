<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\User;
use AppBundle\Form\UserLoginType;
use AppBundle\Form\UserRegistrationType;
use AppBundle\Service\UserManager;
use AppBundle\Utils\SecurityUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @var TwigEngine
     */
    private $twig;
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var SecurityUtils
     */
    private $securityUtils;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Router
     */
    private $router;

    public function __construct(
        TwigEngine $twig, 
        AuthenticationUtils $authenticationUtils, 
        FormFactory $formFactory,
        SecurityUtils $securityUtils,
        UserManager $userManager,
        Session $session,
        Router $router
    )
    {
        $this->twig = $twig;
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
        $this->securityUtils = $securityUtils;
        $this->userManager = $userManager;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @return Response
     */
    public function loginAction()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->securityUtils->getLastUserUsername();
        $form = $this->formFactory->create(UserLoginType::class, ['_username' => $lastUsername]);

        return $this->twig->renderResponse('front/security/login.html.twig', [
            'error' => $error,
            'loginForm' => $form->createView()
        ]);
    }
    
    public function logoutAction()
    {   

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $form = $this->formFactory->create(UserRegistrationType::class, new User());

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->userManager->saveNew($form->getData());
            $this->session->getFlashBag()->add('success', 'You have been successfully registered. You can log in now!');
            return $this->redirect($this->router->generate('front_login'));
        }

        return $this->twig->renderResponse('front/security/register.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }

}