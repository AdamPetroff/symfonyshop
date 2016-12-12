<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction(){
        return $this->render('admin/default/index.html.twig');
    }
}