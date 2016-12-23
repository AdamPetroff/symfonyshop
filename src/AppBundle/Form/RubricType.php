<?php

namespace AppBundle\Form;

use AppBundle\Entity\Rubric;
use AppBundle\Repository\RubricRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RubricType extends AbstractType
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('url', TextType::class, ['label' => 'Seo name', 'required' => false])
            ->add('description')
            ->add('active')
            ->add('main_img', FileType::class, ['required' => false])
            ->add('parent', EntityType::class, [
                'required' => false,
                'class' => Rubric::class,
                'expanded' => false,
                'choice_label' => 'name',
                'multiple' => false,
                'choices' => $this->doctrine->getRepository(Rubric::class)->findOtherThan($builder->getData()),
                'placeholder' => '-- unassigned --'
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        return $builder;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults(['data_class' => Rubric::class]);
    }

    public function getName()
    {
        return 'app_bundle_rubric_type';
    }
}
