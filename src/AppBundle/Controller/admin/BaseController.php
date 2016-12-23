<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Repository\BaseRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends Controller
{
    /** Renders a reponse with additional parameters
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function render($view, array $parameters = array(), Response $response = null)
    {
        return parent::render($view, $parameters, $response);
    }

    /**
     * @param $type
     * @param null $data
     * @param array $options
     * @return FormBuilder
     */
    protected function getFormBuilder($type, $data = null, array $options = array()) : FormBuilder
    {
        return $this->container->get('form.factory')->createBuilder($type, $data, $options);
    }
    
}