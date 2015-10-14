<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext as SC;

/**
 * Description of AuthentificationController
 *
 * @author SGOURMELON
 */
class AuthentificationController extends Controller {
    
     public function loginAction(Request $request)
    {
    /*
     * UNWORKING ONE (YET...)
     */    
         $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SC::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SC::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SC::AUTHENTICATION_ERROR);
            $session->remove(SC::AUTHENTICATION_ERROR);
        }
        return $this->render('AsmoldingBundle:Auth:authentification.html.twig', array(
            // last username entered by the user
            'dernier_identifiant' => $session->get(SC::LAST_USERNAME),
            'error'         => $error,
        ));
    }
}
