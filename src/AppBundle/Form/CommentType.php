<?php

namespace AppBundle\Form;

use AppBundle\Entity\Comment;
use AppBundle\Service\CommentManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
    /**
     * @var CommentManager
     */
    private $commentManager;

    public function __construct(Router $router, CommentManager $commentManager)
    {
        $this->router = $router;
        $this->commentManager = $commentManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('posted_by', TextType::class, ['label' => 'Your nickname'])
            ->add('text', TextareaType::class, ['label' => 'Comment'])
            ->add('parent', HiddenType::class)
            ->add('submit', SubmitType::class)
            ->setAction($this->router->getGenerator()->generate('front_blog_post_comment'));

        $builder->get('parent')->addModelTransformer(new CallbackTransformer(
            function (Comment $parent = null) {
                if ($parent) {
                    return $parent->getId();
                } else {
                    return null;
                }
            },
            function ($commentId) {
                if (!$commentId) {
                    return null;
                }
                return $this->commentManager->getComment($commentId);
            }
        ));

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
