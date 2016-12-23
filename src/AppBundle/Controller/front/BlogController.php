<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Article;
use AppBundle\Form\CommentType;
use AppBundle\Service\CommentManager;
use AppBundle\Service\RubricManager;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class BlogController extends BaseController
{
    /**
     * @var CommentManager
     */
    private $comment_manager;
    /**
     * @var RubricManager
     */
    private $rubric_manager;

    public function __construct(CommentManager $comment_manager, RubricManager $rubric_manager)
    {
        $this->comment_manager = $comment_manager;
        $this->rubric_manager = $rubric_manager;
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response
     * @Route("/article/{url}", name="front_display_article")
     * @ParamConverter(class="AppBundle\Entity\Article")
     */
    public function DisplayArticleAction(Request $request, Article $article)
    {
        $comment_form = $this->createForm(CommentType::class);
        $comment_form->handleRequest($request);
        
        return $this->render(':front/blog:article.html.twig', [
            'article' => $article,
            'comments' => $this->comment_manager->findArticleCommentsOrderedByVotes($article),
            'comment_form' => $comment_form->createView(),
        ]);
    }

    /**
     * @Route("/blog/post_comment", name="front_blog_post_comment")
     * @param Request $request
     * @return JsonResponse
     */
    public function postCommentAction(Request $request)
    {
        $error = true;
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && isset($_POST['article_id'])){
            $article = $this->comment_manager->post($form->getData(), $_POST['article_id']);
            $error = false;
            $this->addFlash('success', 'Thanks for Commenting');
            if($article){
                return new JsonResponse([
                    'error' => $error,
                    'flashes_html' => $this->renderView('front/_includes/_flash_messages.html.twig'),
                    'comments_html' => $this->renderView('front/blog/_all_comments.html.twig', [
                        'comments' => $this->comment_manager->findArticleCommentsOrderedByVotes($article)
                    ])
                ]);
            }
                
        }
        else{
            $this->addFlash('error', 'Form is not submitted or is invalid');
        }
        
        return new JsonResponse([
            'error' => $error,
            'flashes_html' => $this->renderView('front/_includes/_flash_messages.html.twig')
        ]);
    }

    /**
     * @return Response
     * @internal param Request $request
     * @Route("/vote_on_comment", name="front_blog_vote_on_comment")
     */
    public function voteOnCommentAction()
    {
        //TODO - kazdy moze hodnotit len raz
        if (isset($_POST['id']) && isset($_POST['reaction'])) {
            $comment = $this->comment_manager->vote($_POST['id'], $_POST['reaction']);

            return new JsonResponse([
                'html' => $this->renderView('front/blog/_comment.html.twig', ['comment' => $comment]),
                'error' => false,
                'message' => 'Thanks for voting!'
            ]);
        }
        else
        {
            return new JsonResponse([
                'error' => true,
                'message' => 'Bad data'
            ]);
        }
    }

    /**
     * @Route("/blog/{url}", name="front_blog_index")
     * @param string $url
     * @return Response
     */
    public function BlogAction($url = '')
    {
        $rubric = $this->rubric_manager->getRubricByUrl($url);
        $rubrics = $this->rubric_manager->getBaseRubrics();
        $articles = $rubric->getArticles();

        return $this->render('front/blog/blog_index.html.twig', [
            'rubrics' => $rubrics,
            'articles' => $articles,
            'selected_rubric' => $rubric
        ]);
    }
}
