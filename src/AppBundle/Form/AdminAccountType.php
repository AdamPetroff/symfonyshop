<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email', EmailType::class, ['label' => 'E-mail'])
            ->add('roles', ChoiceType::class, ['multiple' => true, 'choices' => ['admin' => 'ROLE_ADMIN', 'super admin' => 'ROLE_SUPER_ADMIN']])
            ->add('is_active', null, ['label' => 'Active'])
            ->add('submit', SubmitType::class);


        return $builder;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }

    public function getName()
    {
        return 'app_bundle_admin_account_type';
    }
}
