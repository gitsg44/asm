<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\ContactRepository;
use Asmolding\Bundle\Entity\ProjetRelContact;
use Asmolding\Bundle\Form\Type\FormPRCType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Description of PRCController
 *
 * @author SGOURMELON
 */
class PRCController extends Controller{
    
    public function managePRCAction($projectId = "", $id = 0, $mode = "", Request $request){
        
        $prc = new ProjetRelContact();
        
        // Récupération du client par l'id Projet
        $repoProject = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project');

        $project = $repoProject->find($projectId);
        
        $company = $project->getCompany();

        // Si l'id renseigné dans la requête est différent de 0, récupération de l'affaire pour modification
        if($id !=0){
            $prc = $em = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:ProjetRelContact')->find($id);
        }
        if(!$prc){
                throw new HttpException(404, 'Ce contact n\'existe pas');
        }
        //creation du formulaire de recherche
        
        $form = $this->createForm(new FormPRCType, $prc);
        
        $form->add('contact', 'entity', array(
          'class' => 'AsmoldingBundle:Contact',
          'property' => 'nameFirstname',
          'query_builder' => function(ContactRepository $er) use($company) {
          return $er->getContactsByCompany($company);},));

      
        // si l'id = 0, on crée un bouton Ajouter
            if ($id == 0 || $mode == "ajout") {
            $form->add('ajouter', 'submit');
            $form->get('project')->setData($project);
            } else {
            $form->add('modifier', 'submit'); // sinon on crée un bouton Modifier
            }
        
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
            
            if ($form->has('ajouter')) {
                //Appel de la méthode d'ajout
                $this->addPRCAction($project, $form);
            } else if ($form->has('modifier')) {
                // appel de la méthode de modification
                $this->editPRCAction($id, $project, $form);
            } 
            
            //Retour vers la vue liste des contacts du projet
            $response =  $this->redirect($this->generateUrl('asmolding_admin_listPRC', array('projectId' => $projectId)));
            return $response;
        }
        
        if($mode == 'supprimer'){
            
            $this->deletePRCAction($id);
            
            //Retour vers la vue liste des affaires
            $response = $this->redirect($this->generateUrl('asmolding_admin_listPRC', array('projectId' => $projectId)));
            return $response;
        }

        //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Project:prc.html.twig';
            return $this->render($template,array('form'=>$formView,'prc'=>$prc, 'project'=>$project));
        
    }
    
       public function addPRCAction($project, $form){

        $em = $this->getDoctrine()->getManager();
        
        $prc = new ProjetRelContact(); 
        $contact = $form->get('contact')->getData();

        $repo = $em->getRepository('AsmoldingBundle:ProjetRelContact');
        
        $persistedPrc = $repo->findBy(array('project' => $project, 'contact' => $contact)); 

        
        // si le contact existe déjà en base on renvoie une erreur
        if($persistedPrc){
            throw new HttpException(500, 'Ce contact est déjà associé au projet');
        }
        else{
            $prc->setProject($project);
            $prc->setContact($contact);
            $prc->setName($contact->getNameFirstname());
        
            $em->persist($prc);                
            $em->flush();             
        }  
    }
   
    public function editPRCAction($id, $project, $form) {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:ProjetRelContact');
        
        $prc = $repository->find($id);
        $contact = $form->get('contact')->getData();
        
        $prc->setProject($project);
        $prc->setContact($contact);
        $prc->setName($contact->getNameFirstname());
        
        $em->persist($prc);

        $em->flush();
        
    }
    
    // Suppression d'un contact rattaché à un projet
    public function deletePRCAction($id) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:ProjetRelContact');

        $prc = $repository->find($id);

        $em->remove($prc);

        $em->flush();
    }
    
    public function listPRCAction($projectId){
        
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:ProjetRelContact');
        $repoProject = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project');
        
        $project = $repoProject->find($projectId);
        $prcs = $repository->findBy(array('project' => $project));
        
        // S'il n'y a pas de contact associé au projet, on renvoie vers la page d'ajout.
        if(!$prcs){
            return $this->redirect($this->generateUrl('asmolding_admin_managePRC', array('projectId' => $projectId, 'id' => 0, 'mode' => 'ajouter')));
        }
        return $this->render('AsmoldingBundle:Admin:listePRC.html.twig', array('prcs' => $prcs, 'project'=>$project));
    }
}
