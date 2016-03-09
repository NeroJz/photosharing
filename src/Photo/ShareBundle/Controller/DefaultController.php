<?php

namespace Photo\ShareBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ShareBundle:Default:index.html.twig', array('name' => $name));
    }
}
