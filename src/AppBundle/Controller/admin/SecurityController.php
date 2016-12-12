<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminAccountType;
use AppBundle\Repository\UserRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="admin_login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $this->addFlash('notice', 'You have been logged out successfully');

        return $this->render('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    public function forgottenPassword()
    {
        
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admins", name="admin_admins")
     */
    public function adminsAction(){
        $repo = $this->getRepo(User::class);
        $admins = $repo->findAll();
        return $this->render(':admin/security:admins.html.twig', [
            'admins' => $admins
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin_account/{admin_id}", name="admin_account")
     */
    public function editAdminAction(Request $request, $admin_id = null)
    {
        $repo = $this->getRepo(User::class);
        if($admin_id){
            $user = $repo->find($admin_id);
        }
        else{
            $user = new User();
        }
        $builder = $this->getFormBuilder(AdminAccountType::class, $user);
        if(!$admin_id){
            $builder->get('password')->setAttribute('required', false);
        }
        
        $form = $builder->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $encoder = $this->get('security.password_encoder');
            if(empty($form->getData()->getPassword())){
                $form->getData()->setPassword($encoder->encodePassword($form->getData(), $repo->getOriginal($form->getData())['password']));
            }
            else{
                $form->getData()->setPassword($encoder->encodePassword($form->getData(), $form->getData()->getPassword()));
            }
            $repo->flush($form->getData());
            $this->addFlash('notice','success!!');
            return $this->redirectToRoute('admin_index');
        }
        
        return $this->render('admin/security/adminAccount.html.twig', [
            'form' => $form->createView(),
            'new_user' => $admin_id ? false : true,
            'subject' => $user
        ]);
    }
}