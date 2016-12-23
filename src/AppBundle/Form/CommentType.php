<?php

namespace AppBundle\Form;

use AppBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGenerator;

class CommentType extends AbstractType
{
    /**
     * @var UrlGenerator
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->getGenerator()->generate('front_blog_post_comment'))
            ->add('posted_by', TextType::class, ['label' => 'Your nickname'])
            ->add('text', TextareaType::class, ['label' => 'Comment'])
            ->add('submit', SubmitType::class);

        return $builder;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Comment::class]);
    }

    public function getName()
    {
        return 'app_bundle_comment_type';
    }
}
