<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 19:34
 */

namespace AppBundle\Controller\admin;


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
    public function indexAction()
    {
        return $this->twig->renderResponse('admin/homepage/index.html.twig');
    }
}