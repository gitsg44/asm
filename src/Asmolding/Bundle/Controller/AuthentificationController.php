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
//use Symfony\Component\Security\Core\Authorization\AuthorizationChecker as SC;
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
        
        /*
         * EX-WORKING ONE
         */
        /*
        $user = new Contact();
        //creation du formulaire
        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('password', 'password')
            ->add('valider', 'submit')
            ->getForm();
        $form->handleRequest($request);
        
        
        if($form->isValid())
        {
           //envoyer en ajout
            $bool =  $this->login($form,$request);
            if($bool){
                if($request->getSession()->get('session_user')['level'] == 1) {
                    return $this->redirect($this->generateUrl('asmolding_admin_homepage'));
                }
                else{
                    return $this->redirect($this->generateUrl('asmolding_client_homepage'));
                }
            }else{
                  //envoi à la vue 
                   $formView = $form->createView();
                   $template= 'AsmoldingBundle:Auth:authentification.html.twig';
                   return $this->render($template,array('form'=>$formView));
            }
        }
          //envoi à la vue 
        $formView = $form->createView();
        $template= 'AsmoldingBundle:Auth:authentification.html.twig';
        return $this->render($template,array('form'=>$formView));
    }
    
    public function logoutAction(Request $request){
        $session = $request->getSession();
        $session->clear();
        return $this->redirect($this->generateUrl('asmolding_login'));
    }
    
    public function login($form,$request){
        
        $user = new Contact();
        $login = $form->get('username')->getData();
        $pwd = $form->get('password')->getData();
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact');
//        $query = $em->createQuery('SELECT u FROM Expeditor\Bundle\Entity\Utilisateur u WHERE u.identifiant = :login AND u.motDePasse = :pwd');
//        $query->setParameters(array(
//                                    'login' => $login,
//                                    'pwd' => $pwd,
//                                ));
//        $user = $query->getResult(); 
        $user =  $repository->findOneBy(
                                array('username' => $login,
                                      'password' => $pwd )
                              ); 
  
        if($user){
            // creer la session utilisateur
            $session = $request->getSession();
             // mise en session pour une réutilisation lors d'une autre requête
            $session->set('session_user', array('id'=>$user->getId(),
                                                'nom'=>$user->getNom(),
                                                'prenom'=>$user->getPrenom(),
                                                'level'=> $user->getLevel()));
            // get the attribute set by another controller in another request
            
            return true;
        }else {
            return false;
        }*/
    }
}
