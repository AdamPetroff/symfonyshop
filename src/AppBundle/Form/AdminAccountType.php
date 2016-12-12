<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminAccountType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class, ['label' => 'Password'])
            ->add('email', EmailType::class, ['label' => 'E-mail'])
            ->add('roles', ChoiceType::class, ['multiple' => true, 'choices' => ['admin' => 'ROLE_ADMIN', 'super admin' => 'ROLE_SUPER_ADMIN']])
            ->add('is_active', CheckboxType::class, ['label' => 'Active', 'required' => false])
            ->add('locked', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)->getForm();


        return $builder;
    }
    
}