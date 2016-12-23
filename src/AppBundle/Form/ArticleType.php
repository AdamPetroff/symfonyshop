<?php

namespace AppBundle\Form;

use AppBundle\Entity\Article;
use AppBundle\Entity\Rubric;
use AppBundle\Repository\RubricRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
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
            ->add('name', TextType::class)
            ->add('url', TextType::class, ['label' => 'Seo name', 'required' => false])
            ->add('perex')
            ->add('text', FroalaEditorType::class)
            ->add('news')
            ->add('rubric', EntityType::class, [
                'class' => Rubric::class,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => 'name',
                'choices' => $this->doctrine->getRepository(Rubric::class)->findProper(),
                'placeholder' => '-- unassigned --'
            ])
            ->add('main_img', FileType::class, ['required' => false, 'data_class' => null])
            ->add('submit', SubmitType::class);
        
        return $builder;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Article::class]);
    }

    public function getName()
    {
        return 'app_bundle_article_type';
    }
}
