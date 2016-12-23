<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Rubric;
use AppBundle\Form\ArticleType;
use AppBundle\Form\RubricType;
use AppBundle\Repository\ArticleRepository;
use AppBundle\Repository\RubricRepository;
use AppBundle\Service\ArticleManager;
use AppBundle\Service\CommentManager;
use AppBundle\Service\RubricManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class BlogController extends BaseController
{
    /**
     * @var ArticleManager
     */
    private $article_manager;
    /**
     * @var RubricManager
     */
    private $rubric_manager;
    /**
     * @var CommentManager
     */
    private $comment_manager;

    public function __construct(ArticleManager $article_manager, RubricManager $rubric_manager, CommentManager $comment_manager)
    {
        $this->article_manager = $article_manager;
        $this->rubric_manager = $rubric_manager;
        $this->comment_manager = $comment_manager;
    }

    /**
     * @Route("/blog", name="admin_blog_index")
     * @return Response
     */
    public function indexAction(){
        $rubrics = $this->rubric_manager->getBaseRubrics();

        return $this->render('admin/blog/index.html.twig', [
            'rubrics' => $rubrics
        ]);
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response
     * @Route("/blog/article/{id}", name="admin_blog_edit_article")
     * @ParamConverter(class="AppBundle\Entity\Article")
     */
    public function editArticleAction(Request $request, Article $article){
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        $delete_form = $this->createFormBuilder()
            ->add('delete', SubmitType::class)
            ->getForm();

        $delete_form->handleRequest($request);
        if($delete_form->isSubmitted()){
            $this->article_manager->delete($article);
            $this->addFlash('success', 'Article was deleted successfully');
            return $this->redirectToRoute('admin_blog_index');
        }
        
        if($form->isSubmitted() && $form->isValid()){
            $this->article_manager->save($form->getData());
            $this->addFlash('success', 'The item was saved successfully');
            
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin/blog/edit_article.html.twig', [
            'form_edit' => $form->createView(),
            'form_delete' => $delete_form->createView(),
            'article' => $article,
        ]);
    }

    /**
     * @Route("/blog/new_article", name="admin_blog_new_article")
     * @param Request $request
     * @return Response
     */
    public function newArticleAction(Request $request)
    {
        $form = $this->createForm(ArticleType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->article_manager->save($form->getData());
            $this->addFlash('success', 'The item was saved successfully');

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin/blog/new_article.html.twig', [
            'form_edit' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Rubric $rubric
     * @return Response
     * @Route("/blog/rubric/{id}", name="admin_blog_edit_rubric")
     * @ParamConverter(class="AppBundle\Entity\Rubric")
     */
    public function editRubricAction(Request $request, Rubric $rubric)
    {
        $form = $this->createForm(RubricType::class, $rubric);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->rubric_manager->save($form->getData());
            $this->addFlash('success', 'The item was saved successfully');
            
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin/blog/edit_rubric.html.twig', [
            'form_edit' => $form->createView(),
            'rubric' => $rubric
        ]);
    }

    /**
     * @Route("/blog/new_rubric", name="admin_blog_new_rubric")
     * @param Request $request
     * @return Response
     */
    public function newRubricAction(Request $request)
    {
        $form = $this->createForm(RubricType::class, new Rubric());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->rubric_manager->save($form->getData());
            $this->addFlash('success', 'The item was saved successfully');

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin/blog/edit_rubric.html.twig', [
            'form_edit' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/manage_comments/{id}", name="admin_article_manage_comments")
     * @ParamConverter()
     * @param Article $article
     * @return Response
     */
    public function manageCommentsAction(Article $article)
    {
        return $this->render('admin/blog/manage_comments.html.twig', [
            'article' => $article,
            'comments' => $this->comment_manager->findArticleCommentsOrderedByVotes($article)
        ]);
    }

    /**
     * @Route("/admin/delete_comment", name="admin_blog_delete_comment")
     */
    public function deleteCommentAction()
    {
        $error = true;
        if(isset($_POST['id'])){
            if($this->comment_manager->delete($_POST['id'])){
                $this->addFlash('success', 'Comment was deleted successfully');
                $error = false;
            }
            else{
                $this->addFlash('error', 'Comment not found');
            }
        }   
        else{
            $this->addFlash('error', 'Bad data');
        }
        return new JsonResponse([
            'error' => $error,
            'flashes_html' => $this->renderView('front/_includes/_flash_messages.html.twig')
        ]);
    }
}
