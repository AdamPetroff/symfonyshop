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
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    public function defaultAction()
    {
        return $this->twig->renderResponse('front/homepage/index.html.twig');
    }
}