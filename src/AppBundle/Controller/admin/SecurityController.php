<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Admin;
use AppBundle\Form\AdminAccountType;
use AppBundle\Form\AdminForgottenPassword;
use AppBundle\Form\AdminLoginType;
use AppBundle\Form\NewAdminType;
use AppBundle\Service\AdminManager;
use AppBundle\Utils\SecurityUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @var AdminManager
     */
    private $adminManager;
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var TwigEngine
     */
    private $twig;
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var SecurityUtils
     */
    private $securityUtils;
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
        AdminManager $adminManager,
        AuthenticationUtils $utils,
        FormFactory $formFactory,
        SecurityUtils $securityUtils,
        Session $session,
        Router $router
    ) {
        $this->adminManager = $adminManager;
        $this->authenticationUtils = $utils;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->securityUtils = $securityUtils;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->securityUtils->getLastAdminUsername();
        $loginForm = $this->formFactory->create(AdminLoginType::class, ['_username' => $lastUsername]);
        $fromLogout = $request->query->get('from_logout');

        if ($fromLogout) {
            $this->session->getFlashBag()->add('notice', 'You have been logged out successfully');
        }

        return $this->twig->renderResponse('admin/security/login.html.twig', [
            'error' => $error,
            'loginform' => $loginForm->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function forgottenPasswordAction(Request $request)
    {
        $form = $this->formFactory->create(AdminForgottenPassword::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $this->adminManager->findByUsername($form->getData()['username']);
            if ($user) {
                $newPassword = $this->adminManager->assignNewPassword($user);
                if (mail($user->getEmail(), 'New password', 'Your new password is ' . $newPassword)) {
                    $this->session->getFlashBag()->add('success', "Your new password has been sent to your email.");
                } else {
                    $this->session->getFlashBag()->add('success',
                        "There was a problem on the server and the mail could not be sent. Please try to resubmit the form");
                }
                return $this->redirect($this->router->generate('admin_login'));
            } else {
                $form->addError(new FormError('The user has not been found. Please check your spelling.'));
            }
        }
        return $this->twig->renderResponse('admin/security/forgottenPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return Response
     */
    public function adminsAction()
    {
        $admins = $this->adminManager->findAll();

        return $this->twig->renderResponse(':admin/security:admins.html.twig', [
            'admins' => $admins
        ]);
    }

    /**
     * @param Request $request
     * @param Admin $user
     * @return Response
     */
    public function editAdminAction(Request $request, Admin $user)
    {
        $form = $this->formFactory->create(AdminAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adminManager->save($form->getData());
            $this->session->getFlashBag()->add('notice', 'success!!');
            return $this->redirect($this->router->generate('admin_index'));
        }

        return $this->twig->renderResponse('admin/security/adminAccount.html.twig', [
            'form' => $form->createView(),
            'subject' => $user
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function newAdminAction(Request $request)
    {
        $form = $this->formFactory->create(NewAdminType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adminManager->saveNew($form->getData());
            $this->session->getFlashBag()->add('notice', 'New admin has been saved');

            return $this->redirect($this->router->generate('admin_index'));
        }

        return $this->twig->renderResponse('admin/security/new_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}