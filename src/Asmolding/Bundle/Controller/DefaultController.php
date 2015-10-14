<?php

namespace Asmolding\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AsmoldingBundle:Auth:authentification.html.twig');
    }
}
