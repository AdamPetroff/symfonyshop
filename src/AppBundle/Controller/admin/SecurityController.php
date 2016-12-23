<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminAccountType;
use AppBundle\Form\AdminForgottenPassword;
use AppBundle\Form\AdminForgottenPassworde;
use AppBundle\Form\NewAdminType;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\AdminManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class SecurityController extends BaseController
{
    //TODO - role do entity
    //TODO - zmena hesla pre adminov
    /**
     * @var AdminManager
     */
    private $admin_manager;

    public function __construct(AdminManager $admin_manager)
    {
        $this->admin_manager = $admin_manager;
    }

    /**
     * @Route("/login", name="admin_login")
     * @return Response
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if(isset($_GET['from_logout'])){
            $this->addFlash('notice', 'You have been logged out successfully');
        }

        return $this->render('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/forgotten_password", name="admin_forgotten_password")
     */
    public function forgottenPasswordAction(Request $request)
    {
        $form = $this->getFormBuilder(AdminForgottenPassword::class)->getForm();
        $form->handleRequest($request);
        if($form->isValid()){
            $user = $this->admin_manager->findByUsername($form->getData()['username']);
            if($user){
                $new_password = $this->admin_manager->assignNewPassword($user);
                //TODO - send new password in email
                $this->addFlash('success', "Your password has been successfully changed. New password : '$new_password'");
                return $this->redirectToRoute('admin_login');
            }
            else{
                $form->addError(new FormError('The user has not been found. Please check your spelling.'));
            }
        }
        return $this->render('admin/security/forgottenPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return Response
     * @Route("/admins", name="admin_admins")
     */
    public function adminsAction(){
        $admins = $this->admin_manager->findAll();
        
        return $this->render(':admin/security:admins.html.twig', [
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
        $form = $this->createForm(AdminAccountType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $this->admin_manager->save($form->getData());
            $this->addFlash('notice','success!!');
            return $this->redirectToRoute('admin_index');
        }
        
        return $this->render('admin/security/adminAccount.html.twig', [
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
        $form = $this->createForm(NewAdminType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->admin_manager->saveNew($form->getData());
            $this->addFlash('notice','New admin has been saved');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/security/new_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}