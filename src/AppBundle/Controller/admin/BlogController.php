<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Article;
use AppBundle\Entity\Rubric;
use AppBundle\Repository\ArticleRepository;
use AppBundle\Repository\RubricRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\UnitOfWork;

class BlogController extends BaseController
{
    /**
     * @Route("/blog", name="admin_blog_index")
     */
    public function indexAction(){
        $this->repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $this->repo->findAll();
        $rubrics_repo = $this->getDoctrine()->getRepository(Rubric::class);
        $rubrics = $rubrics_repo->query('r', ['where' => 'r.parent = 1']);

//        dump($rubrics);die;

        return $this->render('admin/blog/index.html.twig', [
            'articles' => $articles,
            'rubrics' => $rubrics
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/blog/article/{article_id}", name="admin_blog_edit_article")
     */
    public function editArticleAction(Request $request, $article_id = null){
        $this->repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $article_id ? $this->repo->find($article_id) : new Article();

dump($this->get('doctrine')->getRepository(Rubric::class)->findProper(['select' => 'r.name, r.id']));die;
        $form = $this->createFormBuilder($article)
            ->add('name', TextType::class)
            ->add('url', TextType::class, ['label' => 'Seo name', 'required' => false])
            ->add('perex')
            ->add('text')
            ->add('news')
            ->add('deleted')
            ->add('rubric', ChoiceType::class, [
                'data' => $article->getRubric() ? $article->getRubric()->getId() : null,
                'choices' => RubricRepository::getKeyValue($this->get('doctrine')->getRepository(Rubric::class)->findProper(['select' => 'r.name, r.id']), 'id', 'name'),
                'placeholder' => '-- unassigned --'
            ])
            ->add('main_img', FileType::class, ['required' => false, 'data_class' => null])
            ->add('submit', SubmitType::class)
            ->getForm();

		// $form->get('rubric')->addModelTransformer()

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            try{
                $this->repo->flush($form->getData());
                $this->addFlash('notice', 'The item was saved successfully');
            }
            catch (\Throwable $e){
                $this->addFlash('notice', $e->getMessage());
            }
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin/blog/edit_article.html.twig', [
            'form_edit' => $form->createView(),
            'article' => $article,
        ]);
    }

    /**
     * @param Request $request
     * @param null $rubric_id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/blog/rubric/{rubric_id}", name="admin_blog_edit_rubric")
     */
    public function editRubricAction(Request $request, $rubric_id = null)
    {
        $rubrics_repo = $this->getDoctrine()->getRepository(Rubric::class);
        $rubric = $rubric_id ? $rubrics_repo->find($rubric_id) : new Rubric();

        dump($rubric);die;
        $conditions = ['select' => 'r.id,r.name'];
        if($rubric_id){
            $conditions['where'] = "r.id != $rubric_id";
        }
        $all_rubrics = $rubrics_repo->query('r', $conditions);
        $form = $this->createFormBuilder($rubric)
            ->add('name')
            ->add('url', TextType::class, ['label' => 'Seo name', 'required' => false])
            ->add('description')
            ->add('active')
            ->add('deleted')
            ->add('main_img', FileType::class, ['required' => false])
            ->add('parent', ChoiceType::class, ['data' => $rubric->getParent() ? $rubric->getParent()->getId() : null,
                'required' => false,
                'choices' => RubricRepository::getKeyValue($all_rubrics, 'id', 'name'),
                'placeholder' => '-- unassigned --'
            ])
            ->add('special_access')
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            try{
                $rubrics_repo->flush($form->getData());
                $this->addFlash('notice', 'The item was saved successfully');
            }
            catch (\Throwable $e){
                $this->addFlash('notice', $e->getMessage());
            }
            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin/blog/edit_rubric.html.twig', [
            'form_edit' => $form->createView(),
            'rubric' => $rubric
        ]);
    }
}
