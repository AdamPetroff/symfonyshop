<?php

namespace AppBundle\Form;

use AppBundle\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class, ['label' => 'Password'])
            ->add('email', EmailType::class, ['label' => 'E-mail'])
            ->add('roles', ChoiceType::class,
                ['multiple' => true, 'choices' => ['admin' => 'ROLE_ADMIN', 'super admin' => 'ROLE_SUPER_ADMIN']])
            ->add('isActive', null, ['label' => 'Active'])
            ->add('submit', SubmitType::class);

        return $builder;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Admin::class]);
    }

    public function getName()
    {
        return 'app_bundle_new_admin_type';
    }
}
