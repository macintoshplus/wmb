<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JbNahanWorkflowManagerBundle:Default:index.html.twig', array('name' => $name));
    }
}
