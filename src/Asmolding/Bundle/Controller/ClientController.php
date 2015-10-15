<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\Contact;
use Asmolding\Bundle\Form\Type\ChangePasswordType;
use Asmolding\Bundle\Form\Type\FormContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Description of ClientController
 *
 * @author SGOURMELON
 */
class ClientController extends Controller{
    
    public function indexAction(Request $request){
       
        $user = $this->getUser();
        $prenom = $user->getFirstname();
        $nom = $user->getName();
        $name = $prenom . ' ' . $nom;
        return $this->render('AsmoldingBundle:Client:accueilClient.html.twig', array('name' => $name));
    }
    
    public function manageProfileAction($id, $mode="", Request $request){
        
        //creation d'un nouvel objet contact
        $contact = new Contact();
        
        // Récupération des infos du contact
        $contact = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact')->find($id);
        if($contact){
            $company = $contact->getCompany();
        }
        //creation du formulaire contact
        $form = $this->createForm(new FormContactType, $contact);
       
        $form ->remove('level');
        $form ->remove('cp');
        
        if($mode == 'modifier'){
            $form->add('modifier', 'submit'); 
        } 
           
        
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
            
             if ($form->has('modifier')) {
                $this->editProfileAction($id, $company, $form);
             }
            
            //Retour vers la vue profil
            $response = $this->redirect($this->generateUrl('asmolding_client_viewProfile', array('id' => $id)));
            return $response; 
        }
        
        //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Contact:profil.html.twig';
            return $this->render($template,array('form'=>$formView,'contact'=>$contact));
    }
    
    public function editProfileAction($id, $company, $form){
    
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Contact');
        
        $contact = $repository->find($id);
      
        $contact->setCompany($company);
        $contact->setFirstname(str_replace(' ', '-', ucwords(str_replace('-',' ', $form->get('firstname')->getData()))));
        $contact->setName(mb_strtoupper($form->get('name')->getData()));
        $contact->setUsername($form->get('username')->getData());
        $contact->setEmail($form->get('email')->getData());
        $contact->setPhone($form->get('phone')->getData());
        $contact->setFax($form->get('fax')->getData());
        $contact->setMobile($form->get('mobile')->getData());
        $contact->setPrivatePhone($form->get('privatePhone')->getData());
        $contact->setDepartment($form->get('department')->getData());
        $contact->setInsertBy('company');

        $em->persist($contact);

        $em->flush();
    }
    
    public function viewProfileAction($id){
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact');

        $contact = $repository->find($id);
        return $this->render('AsmoldingBundle:Contact:viewProfile.html.twig', array('contact' => $contact));
    }
    
    // Modification du mot de passe
    public function changePasswordAction($id, Request $request)
    {
      $contact = new Contact();
      $form = $this->createForm(new ChangePasswordType(), $contact);

      $form->add('modifier', 'submit');
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          
          $em = $this->getDoctrine()->getManager();
          $repository = $em->getRepository('AsmoldingBundle:Contact');
          $contact = $repository->find($id);
          
          $contact->setPassword($form->get('password')->getData());
          
          $em->persist($contact);
          $em->flush();
          
          //Retour vers la vue profil
          $response = $this->redirect($this->generateUrl('asmolding_client_viewProfile', array('id' => $id)));
          return $response;
      }

      //envoi à la vue 
      $formView = $form->createView();
      $template= 'AsmoldingBundle:Contact:changePassword.html.twig';
      return $this->render($template,array('form'=>$formView));      
    }
    
    public function listProjectsAction(){
        
        $user = $this->getUser();
        
        $projects = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project')-> getProjectsByUser($user);
        
        return $this->render('AsmoldingBundle:Client:viewProjects.html.twig', array('projects' => $projects));
    }
    
    public function planningGeneralAction($projectId){
        
        $user = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        $project = $em->getRepository('AsmoldingBundle:Project')->find($projectId);
       
        $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByUserAndByProject($user, $project);
        if(!$molds){
            throw new HttpException(404, 'Pas d\'affaires');
        }
        
        $milestones = $em->getRepository('AsmoldingBundle:Milestone')->getAllVisible();    
        
        $generalPlans = $em->getRepository('AsmoldingBundle:GeneralPlan')->getGeneralPlansByUser($user);
        
        foreach ($molds as $mold){
            $project = $mold->getProject();
            $lastUpdate = $em->getRepository('AsmoldingBundle:GeneralPlan')->getRecentUpdateTimeByProject($project);
        }
        
        
        
        $today = date('d-m-Y');
        $numWeek = date("W", strtotime($today));
        $year = date("Y", strtotime($today));
        
        return $this->render('AsmoldingBundle:Client:generalPlanClient.html.twig', array('molds' => $molds, 'generalPlans' => $generalPlans,'milestones' => $milestones,'numWeek' => $numWeek, 'year' => $year, 'lastUpdate' => $lastUpdate));
    }
    
}
