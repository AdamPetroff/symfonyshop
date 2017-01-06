<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 15. 12. 2016
 * Time: 16:30
 */

namespace AppBundle\Controller\front;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends Controller
{
    /**
     * @var TwigEngine
     */
    private $twig;

    public function __construct(TwigEngine $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="front_homepage")
     */
    public function defaultAction()
    {
        return $this->twig->renderResponse('front/homepage/index.html.twig');
    }
}