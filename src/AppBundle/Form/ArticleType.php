<?php

namespace AppBundle\Form;

use AppBundle\Entity\Article;
use AppBundle\Entity\Rubric;
use AppBundle\Service\RubricManager;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{

    /**
     * @var RubricManager
     */
    private $rubricManager;

    public function __construct(RubricManager $rubricManager)
    {

        $this->rubricManager = $rubricManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('url', TextType::class, ['label' => 'Seo name', 'required' => false])
            ->add('perex')
            ->add('text', CKEditorType::class, [
                'config' => [
                    'uiColor' => '#ffffff',
                ]
            ])
            ->add('rubric', EntityType::class, [
                'class' => Rubric::class,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => 'name',
                'choices' => $this->rubricManager->getRubricsList(),
                'placeholder' => '-- unassigned --'
            ])
            ->add('tmpMainImgFile', FileType::class,
                ['required' => false, 'label' => 'Main image', 'data_class' => null])
            ->add('submit', SubmitType::class, [
                'attr' => ['formnovalidate' => true]
            ]);

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
