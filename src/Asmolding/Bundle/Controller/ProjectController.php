<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\CompanyRepository;
use Asmolding\Bundle\Entity\ContactRepository;
use Asmolding\Bundle\Entity\Project;
use Asmolding\Bundle\Form\Type\FormProjectType;
use Asmolding\Bundle\Form\Type\FormSearchType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of ProjectController
 *
 * @author SGOURMELON
 */
class ProjectController extends Controller{
    
    public function manageProjectsAction($id = 0, $mode = "", Request $request, $search = ""){
        //creation d'un nouvel objet Project
        $project = new Project();
        
        // Récupération de la date ACTUELLE, du jour et du mois pour remplissage de la premère partie du numéro de projet
        $today = date('d-m-Y');
        $day = date('d', strtotime($today));
        $month = date('m', strtotime($today));

        $asm = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Company')->find(1);
        // Si l'id renseigné dans la requête est différent de 0, récupération du project pour modification
        if($id !=0){
            $project = $em = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project')->find($id);
        }
        if(!$project){
                throw new HttpException(404, 'Ce projet n\'existe pas');
        }
        //creation du formulaire company
        $form = $this->createForm(new FormProjectType(), $project);
        
        $form->add('cp', 'entity', array(
          'class' => 'AsmoldingBundle:Contact',
          'property' => 'nameFirstname',
          'empty_value' => 'Choisissez un Chef de projet',
          'query_builder' => function(ContactRepository $er) use($asm) {
            return $er->getContactsByCompany($asm);},));
            
            // si l'id = 0, on crée un bouton Ajouter
            if ($id == 0 || $mode == "ajout") {
                if($search != ""){
                $form->remove('company');
                $form->add('company', 'entity', array(
                    'class' => 'AsmoldingBundle:Company',
                    'property' => 'name',
                    'empty_value' => 'Choisissez un client',
                    'query_builder' => function(CompanyRepository $er) use($search) {
                        return $er->searchCompanies($search);},));
                }
                $form->add('ajouter', 'submit');
                if($day >= '01' && $month < '07'){
                    $form->get('num1')->setData(date('y', strtotime($today)));
                }
                elseif ($day >= '01' && $month >= '07'){
                    $form->get('num1')->setData(date('y', strtotime('+1 year')));
                }
            } else {
            $form->add('modifier', 'submit'); // sinon on crée un bouton Modifier
            $form->get('num1')->setData(substr($project->getNum(), 0, 2));
            $form->get('num2')->setData(substr($project->getNum(), 3, 5));
            }
            
        
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
            
            if ($form->has('ajouter')) {
                //Appel de la méthode d'ajout
                $this->addProject($form);
                // Appel de la méthode de création du patchcode projet
                $this->createProjectPathCode($form);
            } else if ($form->has('modifier')) {
                // appel de la méthode de modification
                $this->editProject($id, $form);
                
            } 
            
            //Retour vers la vue liste des projets
            $response = $this->redirect($this->generateUrl('asmolding_admin_listProjects'));
            return $response;
        }
        
        if($mode == 'supprimer'){
            
            $this->deleteProject($id);
            
            //Retour vers la vue liste des projets
            $response = $this->redirect($this->generateUrl('asmolding_admin_listProjects'));
            return $response;
        }
        
        //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Project:project.html.twig';
            return $this->render($template,array('form'=>$formView,'project'=>$project));
    }

     public function searchProjectsAction(Request $request){
        $project = new Project();

        $form = $this->createForm(new FormSearchType, $project);
        
        $form->remove('name');
        $form->add('company', 'text', array('constraints' => array(new NotBlank,)));
        $form->add('rechercher', 'submit');
        
        $form->handleRequest($request);
        
        
        if($form->isValid()){
            $companyName = $form->get('company')->getData();

            //Retour vers la vue liste de recherche
            $response = $this->redirect($this->generateUrl('asmolding_admin_listFoundProjects', array('companyName' => $companyName)));
           
            return $response;
        } 
        
         //envoi à la vue recherche
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Search:search.html.twig';
            return $this->render($template,array('form'=>$formView,'project'=>$project));
         
    }
    
    public function addProject($form){
        
        $em = $this->getDoctrine()->getManager();
        
        $project = new Project(); 
        
        $num1 = $form->get('num1')->getData();
        $num2 = $form->get('num2')->getData();
        
        $num = $num1 . '_' . $num2;
        
        $project->setCompany($form->get('company')->getData());
        $project->setNum($num);
        $project->setName($form->get('name')->getData());
        $project->setCp($form->get('cp')->getData());
        $project->setDescription($form->get('description')->getData());
        $project->setDate(new DateTime);
        $project->setPlanStart($form->get('planstart')->getData());
        
        $em->persist($project);                
        $em->flush();
    }
   
    public function editProject($id, $form) {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Project');
        
        $project = $repository->find($id);
        
        $num1 = $form->get('num1')->getData();
        $num2 = $form->get('num2')->getData();
        
        $num = $num1 . '_' . $num2;
        
        $project->setCompany($form->get('company')->getData());

        $name = $form->get('name')->getData();
        $project->setName($name);
        $nameWs = str_replace(' ', '', $name);
        // Si la chaine obtenue fait moins de 8 caractères, on comble par des "X" jusqu'à avoir une chaine de 8 caractères
        if(strlen($nameWs) < 8){
            $nameX = str_pad($nameWs, 8, "X", STR_PAD_RIGHT);
        }
        else{
            $nameX = $nameWs;
        }
        // Création du pathcode Projet (id + 8 premières lettres du nom du projet)
        $chiffres = array("0","1","2","3","4","5","6","7","8","9");
        $nameWf = ltrim($nameX, '0123456789');
        $newName = strtoupper(str_replace($chiffres, 'X', $nameWf));
        $chaine = $id . substr($newName, 0, 8);
        $pathcode = str_pad($chaine, 12, "0", STR_PAD_LEFT);
        
        $project->setNum($num);
        $project->setCp($form->get('cp')->getData());
        $project->setPathcode($pathcode);
        $project->setDescription($form->get('description')->getData());
        $project->setPlanStart($form->get('planstart')->getData());
        
        $em->persist($project);

        $em->flush();
        
    }
    
    // Suppression d'un projet
    public function deleteProject($id) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Project');

        $project = $repository->find($id);

        $em->remove($project);

        $em->flush();
    }
    
    public function listProjectsAction(){
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project');

        $projects = $repository->getAll();
        if(!$projects){
            return new Response('Aucun projet existant. Crééez un projet');
        }
        return $this->render('AsmoldingBundle:Admin:listeProjects.html.twig', array('projects' => $projects));
    }
    
    public function listFoundProjectsAction($companyName) {

        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project');
        
        $foundProjects = $repository->searchProjectsByCompanyName($companyName);
        
        if(!$foundProjects){
             throw new NotFoundHttpException('Aucun projet pour le client commençant par "' . $companyName . '" trouvé');  
        }
        return $this->render('AsmoldingBundle:Admin:listeFoundProjects.html.twig', array('foundProjects' => $foundProjects, 'companyName' => $companyName));
    }
    
     // Création du pathcode projet
    public function createProjectPathCode($form){
        
        // Création du pathcode (id + 8 premières lettres du nom du projet (sichiffres ==> X)
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Project');
        
        $name = $form->get('name')->getData();
        
        $project = $repository->findOneBy(array('name' => $name,));
        
        $idProject = $project->getId();
        $nameWs = str_replace(' ', '', $name);
        // Si la chaine obtenue fait moins de 8 caractères, on comble par des "X" jusqu'à avoir une chaine de 8 caractères
        if(strlen($nameWs) < 8){
            $nameX = str_pad($nameWs, 8, "X", STR_PAD_RIGHT);
        }
        else{
            $nameX = $nameWs;
        }
        // Création du pathcode Projet (id + 8 premières lettres du nom du projet)
        $chiffres = array("0","1","2","3","4","5","6","7","8","9");
        $nameWf = ltrim($nameX, '0123456789');
        $newName = strtoupper(str_replace($chiffres, 'X', $nameWf));
        $chaine = $idProject . substr($newName, 0, 8);
        $pathcode = str_pad($chaine, 12, "0", STR_PAD_LEFT);
        
        $project->setPathcode($pathcode);
        
        $em->persist($project);                
        $em->flush();
    }
}
