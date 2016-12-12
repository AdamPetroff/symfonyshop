<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends Controller
{
    /**
     * @var BaseRepository
     */
    protected $repo;

    protected $template_var = [];

    /** Renders a reponse with additional parameters
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function render($view, array $parameters = array(), Response $response = null)
    {
        $parameters['test'] = 'funguje';
        return parent::render($view, $parameters, $response);
    }
    
    protected function setRepo($repo){
        $this->repo = $repo;
    }
    
    protected function getRepo($repo){
        return $this->get('doctrine')->getRepository($repo);
    }

    /*
     * @return FormBuilder
     */
    protected function getFormBuilder($type, $data = null, array $options = array()) : FormBuilder
    {
        return $this->container->get('form.factory')->createBuilder($type, $data, $options);
    }
    
    
    
}