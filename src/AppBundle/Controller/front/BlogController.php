<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Service\ArticleManager;
use AppBundle\Service\CommentManager;
use AppBundle\Service\CommentVoteManager;
use AppBundle\Service\RubricManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class BlogController extends Controller
{
    /**
     * @var CommentManager
     */
    private $commentManager;
    /**
     * @var RubricManager
     */
    private $rubricManager;
    /**
     * @var TwigEngine
     */
    private $twig;
    /**
     * @var ArticleManager
     */
    private $articleManager;
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var CommentVoteManager
     */
    private $commentVoteManager;

    public function __construct(
        TwigEngine $twig,
        CommentManager $commentManager,
        RubricManager $rubricManager,
        ArticleManager $articleManager,
        FormFactory $formFactory,
        Session $session,
        AuthorizationChecker $authorizationChecker,
        TokenStorage $tokenStorage,
        CommentVoteManager $commentVoteManager
    ) {
        $this->commentManager = $commentManager;
        $this->rubricManager = $rubricManager;
        $this->twig = $twig;
        $this->articleManager = $articleManager;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->commentVoteManager = $commentVoteManager;
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function displayArticleAction(Request $request, Article $article)
    {
        $commentForm = $this->formFactory->create(CommentType::class);
        $commentForm->handleRequest($request);

        return $this->twig->renderResponse('front/blog/article.html.twig', [
            'article' => $article,
            'comments' => $this->commentManager->findArticleBaseCommentsOrderedByDate($article),
            'comment_form' => $commentForm->createView(),
            'selectedRubric' => $article->getRubric()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postCommentAction(Request $request)
    {
        $error = true;
        $form = $this->formFactory->create(CommentType::class);
        $form->handleRequest($request);

        $articleId = $request->request->get('article_id');
        if ($articleId) {
            $article = $this->articleManager->getArticle($articleId);
        }

        if ($form->isSubmitted() && $form->isValid() && isset($article)) {
            $this->commentManager->postComment($form->getData(), $article);
            $error = false;
            $this->session->getFlashBag()->add('success', 'Thanks for Commenting');
            if ($article) {
                return new JsonResponse([
                    'error' => $error,
                    'flashes_html' => $this->twig->render('front/_includes/_flash_messages.html.twig'),
                    'comments_html' => $this->twig->render('front/_includes/_all_comments.html.twig', [
                        'comments' => $this->commentManager->findArticleBaseCommentsOrderedByDate($article)
                    ])
                ]);
            }

        } else {
            $this->session->getFlashBag()->add('error', 'The comment could not be posted');
        }

        return new JsonResponse([
            'error' => $error,
            'flashes_html' => $this->twig->render('front/_includes/_flash_messages.html.twig')
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Twig_Error
     */
    public function voteOnCommentAction(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$this->authorizationChecker->isGranted('ROLE_USER') || !$user instanceof User) {
            return new JsonResponse([
                'error' => true,
                'type' => 'unauthorized',
                'message' => 'You have to be logged in to vote on comments'
            ]);
        }

        $commentId = $request->request->get('commentId');
        if ($commentId) {
            $comment = $this->commentManager->getComment($commentId);
        }
        $reaction = $request->request->get('reaction');


        if (isset($comment) && isset($reaction)) {
            if ($this->commentVoteManager->hasUserVotedOnComment($user, $comment)) {
                return new JsonResponse([
                    'error' => true,
                    'message' => 'You have already voted on this comment'
                ]);
            }
            $this->commentVoteManager->voteOnComment($user, $comment, $reaction);

            return new JsonResponse([
                'html' => $this->twig->render('front/_includes/_comment.html.twig', ['comment' => $comment]),
                'error' => false,
                'message' => 'Thanks for voting!'
            ]);
        } else {
            return new JsonResponse([
                'error' => true,
                'message' => 'So sorry. The operation has failed due to bad data.'
            ]);
        }
    }

    /**
     * @param string $url
     * @return Response
     */
    public function blogAction($url = '')
    {
        $rubric = $this->rubricManager->getRubricByUrl($url);
        if (!$rubric) {
            $rubric = $this->rubricManager->getRubric(1);
        }
        $articles = $rubric->getArticles();

        return $this->twig->renderResponse('front/blog/blog_index.html.twig', [
            'articles' => $articles,
            'selectedRubric' => $rubric
        ]);
    }

    public function renderSidebarAction($selectedRubric)
    {
        $rubrics = $this->rubricManager->getBaseRubrics();

        return $this->twig->renderResponse('front/_includes/_sidebar.html.twig', [
            'rubrics' => $rubrics,
            'selectedRubric' => $selectedRubric
        ]);
    }

    /**
     * @param int|null $parentId
     * @return Response
     */
    public function renderCommentFormAction($parentId = null)
    {
        $comment = new Comment();
        if ($parentId) {
            $parent = $this->commentManager->getComment($parentId);
            if ($parent) {
                $comment->setParent($parent);
            }
        }
        $commentForm = $this->formFactory->create(CommentType::class, $comment);

        return $this->twig->renderResponse('front/_includes/_comment_form.html.twig', [
            'commentForm' => $commentForm->createView()
        ]);
    }
}
