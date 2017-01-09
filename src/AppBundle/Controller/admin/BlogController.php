<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Article;
use AppBundle\Entity\Rubric;
use AppBundle\Form\ArticleType;
use AppBundle\Form\RubricType;
use AppBundle\Service\ArticleManager;
use AppBundle\Service\CommentManager;
use AppBundle\Service\RubricManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BlogController extends Controller
{
    /**
     * @var ArticleManager
     */
    private $articleManager;
    /**
     * @var RubricManager
     */
    private $rubricManager;
    /**
     * @var CommentManager
     */
    private $commentManager;
    /**
     * @var TwigEngine
     */
    private $twig;
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var Session
     */
    private $session;

    public function __construct(
        TwigEngine $twig, 
        ArticleManager $articleManager,
        RubricManager $rubricManager,
        CommentManager $commentManager,
        FormFactory $formFactory,
        Session $session
    )
    {
        $this->articleManager = $articleManager;
        $this->rubricManager = $rubricManager;
        $this->commentManager = $commentManager;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->session = $session;
    }

    /**
     * @return Response
     */
    public function indexAction(){
        $rubrics = $this->rubricManager->getBaseRubrics();
        $articles = $this->articleManager->getNonDeletedArticles();

        return $this->twig->renderResponse('admin/blog/index.html.twig', [
            'rubrics' => $rubrics,
            'articles' => $articles
        ]);
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function editArticleAction(Request $request, Article $article){
        $form = $this->formFactory->create(ArticleType::class, $article);
        $form->handleRequest($request);
        
        $deleteForm = $this->formFactory->createBuilder()
            ->add('delete', SubmitType::class)
            ->getForm();

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted()){
            $this->articleManager->deleteArticle($article);
            $this->session->getFlashBag()->add('success', 'Article was deleted successfully');
            return $this->redirectToRoute('admin_blog_index');
        }
        
        if($form->isSubmitted() && $form->isValid()){
            $this->articleManager->saveArticle($form->getData());
            $this->session->getFlashBag()->add('success', 'The item was saved successfully');
            
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->twig->renderResponse('admin/blog/edit_article.html.twig', [
            'form_edit' => $form->createView(),
            'form_delete' => $deleteForm->createView(),
            'article' => $article,
        ]);
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @return Response
     */
    public function newArticleAction(Request $request)
    {
        $form = $this->formFactory->create(ArticleType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->articleManager->saveArticle($form->getData());
            $this->session->getFlashBag()->add('success', 'The item was saved successfully');

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->twig->renderResponse('admin/blog/new_article.html.twig', [
            'form_edit' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Rubric $rubric
     * @return Response
     */
    public function editRubricAction(Request $request, Rubric $rubric)
    {
        $form = $this->formFactory->create(RubricType::class, $rubric);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->rubricManager->save($form->getData());
            $this->session->getFlashBag()->add('success', 'The item was saved successfully');
            
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->twig->renderResponse('admin/blog/edit_rubric.html.twig', [
            'form_edit' => $form->createView(),
            'rubric' => $rubric
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function newRubricAction(Request $request)
    {
        $form = $this->formFactory->create(RubricType::class, new Rubric());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->rubricManager->save($form->getData());
            $this->session->getFlashBag()->add('success', 'The item was saved successfully');

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->twig->renderResponse('admin/blog/edit_rubric.html.twig', [
            'form_edit' => $form->createView()
        ]);
    }

    /**
     * @param Article $article
     * @return Response
     */
    public function manageCommentsAction(Article $article)
    {
        return $this->twig->renderResponse('admin/blog/manage_comments.html.twig', [
            'article' => $article,
            'comments' => $this->commentManager->findArticleBaseCommentsOrderedByVotes($article)
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Twig_Error
     */
    public function deleteCommentAction(Request $request)
    {
        $error = true;
        $commentId = $request->request->get('comment_id');
        $comment = $this->commentManager->getComment($commentId);
        if($comment){
            if($this->commentManager->deleteComment($comment)){
                $this->session->getFlashBag()->add('success', 'Comment was deleted successfully');
                $error = false;
            }
            else{
                $this->session->getFlashBag()->add('error', 'Comment could not be deleted');
            }
        }   
        else{
            $this->session->getFlashBag()->add('error', 'Comment was not found');
        }
        return new JsonResponse([
            'error' => $error,
            'flashes_html' => $this->twig->render('front/_includes/_flash_messages.html.twig')
        ]);
    }
}
