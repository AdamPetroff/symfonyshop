<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminAccountType;
use AppBundle\Form\AdminForgottenPassword;
use AppBundle\Form\NewAdminType;
use AppBundle\Service\AdminManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    private $authentication_utils;
    /**
     * @var TwigEngine
     */
    private $twig;
    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(TwigEngine $twig, AdminManager $adminManager, AuthenticationUtils $utils, FormFactory $formFactory)
    {
        $this->adminManager = $adminManager;
        $this->authentication_utils = $utils;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/login", name="admin_login")
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $error = $this->authentication_utils->getLastAuthenticationError();
        $lastUsername = $this->authentication_utils->getLastUsername();
        $fromLogout = $request->query->get('from_logout');
        if($fromLogout){
            $this->addFlash('notice', 'You have been logged out successfully');
        }

        return $this->twig->renderResponse('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/forgotten_password", name="admin_forgotten_password")
     * @param Request $request
     * @return Response
     */
    public function forgottenPasswordAction(Request $request)
    {
        $form = $this->formFactory->create(AdminForgottenPassword::class);
        $form->handleRequest($request);
        if($form->isValid()){
            $user = $this->adminManager->findByUsername($form->getData()['username']);
            if($user){
                $newPassword = $this->adminManager->assignNewPassword($user);
                //TODO - send new password in email
                $this->addFlash('success', "Your password has been successfully changed. New password : '$newPassword'");
                return $this->redirectToRoute('admin_login');
            }
            else{
                $form->addError(new FormError('The user has not been found. Please check your spelling.'));
            }
        }
        return $this->twig->renderResponse('admin/security/forgottenPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return Response
     * @Route("/admins", name="admin_admins")
     */
    public function adminsAction(){
        $admins = $this->adminManager->findAll();
        
        return $this->twig->renderResponse(':admin/security:admins.html.twig', [
            'admins' => $admins
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     * @Route("/admin_account/{id}", name="admin_account")
     */
    public function editAdminAction(Request $request, User $user)
    {
        $form = $this->formFactory->create(AdminAccountType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $this->adminManager->save($form->getData());
            $this->addFlash('notice','success!!');
            return $this->redirectToRoute('admin_index');
        }
        
        return $this->twig->renderResponse('admin/security/adminAccount.html.twig', [
            'form' => $form->createView(),
            'subject' => $user
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/new_admin", name="admin_security_new_admin")
     */
    public function newAdminAction(Request $request)
    {
        $form = $this->formFactory->create(NewAdminType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->adminManager->saveNew($form->getData());
            $this->addFlash('notice','New admin has been saved');
            return $this->redirectToRoute('admin_index');
        }

        return $this->twig->renderResponse('admin/security/new_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}