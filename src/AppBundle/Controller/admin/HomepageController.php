<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 19:34
 */

namespace AppBundle\Controller\admin;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomepageController extends BaseController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction()
    {
        return $this->render('admin/homepage/index.html.twig');
    }
}