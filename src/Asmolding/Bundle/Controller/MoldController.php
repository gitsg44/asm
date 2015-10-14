<?php

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\Mold;
use Asmolding\Bundle\Form\Type\FormMoldType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MoldController
 *
 * @author SGOURMELON
 */
class MoldController extends Controller {
   
    public function manageMoldsAction($projectId = "", $id = 0, $mode = "", Request $request){
        //creation d'un nouvel objet Mold
        $mold = new Mold();
        
        // Récupération du client par l'id Projet
        $repoProject = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project');

        $project = $repoProject->find($projectId);
        
        $company = $project->getCompany();
        
        // Si l'id renseigné dans la requête est différent de 0, récupération de l'affaire pour modification
        if($id !=0){
            $mold = $em = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Mold')->find($id);
        }
        if(!$mold){
                throw new HttpException(404, 'Cette affaire n\'existe pas');
        }
        //creation du formulaire affaire
        $form = $this->createForm(new FormMoldType(), $mold);
            
        $form->add('numProject', 'text');
             
        $numProject = $project->getNum();

        $form->get('numProject')->setData($numProject);
        
            // si l'id = 0, on crée un bouton Ajouter
            if ($id == 0 || $mode == "ajout") {
            $form->add('ajouter', 'submit');
            $form->get('company')->setData($company);
            $form->get('project')->setData($project);
            
            } else {
                $form->add('modifier', 'submit'); // sinon on crée un bouton Modifier
                $form->get('num1')->setData(substr($mold->getNum(), 0, 3));
                $form->get('das')->setData(substr($mold->getNum(), 4, 2));
                $form->get('num2')->setData(substr($mold->getNum(), 7, 3));
            }
            
        
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
            
            if ($form->has('ajouter')) {
                //Appel de la méthode d'ajout
                $this->addMold($project, $form);
                // Appel de la méthode de création du patchcode affaire
                $this->createMoldPathCode($form);
            } else if ($form->has('modifier')) {
                // appel de la méthode de modification
                $this->editMold($id, $project, $form);
            } 
            
            //Retour vers la vue liste des projets
            $response = $this->redirect($this->generateUrl('asmolding_admin_listMolds', array('projectId' => $projectId)));
                    //$this->forward('AsmoldingBundle:Admin:listMolds', array('projectId' => $projectId));
            return $response;
        }
        
        if($mode == 'supprimer'){
            
            $this->deleteMold($id);
            //$this->listUsersAction();
            
            //Retour vers la vue liste des affaires
            $response = $this->redirect($this->generateUrl('asmolding_admin_listMolds', array('projectId' => $projectId)));
                    //$this->forward('AsmoldingBundle:Admin:listMolds', array('projectId' => $projectId));
            return $response;
        } else if($mode == 'archiver'){
            $em = $this->getDoctrine()->getManager();
            $mold = $em->getRepository('AsmoldingBundle:Mold')->find($id);
            
            $mold->setIsArchived(true);
            $em->flush();
            
            //Retour vers la vue liste des affaires
            $response = $this->redirect($this->generateUrl('asmolding_admin_listMolds', array('projectId' => $projectId)));
                    //$this->forward('AsmoldingBundle:Admin:listMolds', array('projectId' => $projectId));
            return $response;
        } else if($mode == 'restaurer'){
            $em = $this->getDoctrine()->getManager();
            $mold = $em->getRepository('AsmoldingBundle:Mold')->find($id);
            
            $mold->setIsArchived(false);
            $em->flush();
            
            //Retour vers la vue liste des affaires
            $response = $this->redirect($this->generateUrl('asmolding_admin_listMolds', array('projectId' => $projectId)));
                    //$this->forward('AsmoldingBundle:Admin:listMolds', array('projectId' => $projectId));
            return $response;
        }
        
        //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Project:mold.html.twig';
            return $this->render($template,array('form'=>$formView,'mold'=>$mold, 'company'=>$company, 'project'=>$project));
    }
    
    public function archiveAllAction($projectId){
        
        $em = $this->getDoctrine()->getManager();
        
        $project = $em->getRepository('AsmoldingBundle:Project')->find($projectId);

        $molds = $em->getRepository('AsmoldingBundle:Mold')->findByProject($project);
        
        foreach ($molds as $mold){
            
           $mold->setIsArchived(true);
           $em->flush();
        }
        
        //Retour vers la vue liste des affaires
            $response = $this->redirect($this->generateUrl('asmolding_admin_listMolds', array('projectId' => $projectId)));
                    //$this->forward('AsmoldingBundle:Admin:listMolds', array('projectId' => $projectId));
            return $response;
    }
    
    public function restoreAllAction($projectId){
        
        $em = $this->getDoctrine()->getManager();
        
        $project = $em->getRepository('AsmoldingBundle:Project')->find($projectId);

        $molds = $em->getRepository('AsmoldingBundle:Mold')->findByProject($project);
        
        foreach ($molds as $mold){
            
           $mold->setIsArchived(false);
           $em->flush();
        }
        
        //Retour vers la vue liste des affaires
            $response = $this->redirect($this->generateUrl('asmolding_admin_listMolds', array('projectId' => $projectId)));
                    //$this->forward('AsmoldingBundle:Admin:listMolds', array('projectId' => $projectId));
            return $response;
    }

    public function addMold($project, $form){
        
        $em = $this->getDoctrine()->getManager();
       
        $company = $project->getCompany();
        
        $mold = new Mold(); 
        
        $num1 = $form->get('num1')->getData();
        $das = $form->get('das')->getData();
        $num2 = $form->get('num2')->getData();
        
        $num = $num1 . '_' . $das . '_' .  $num2;
        
        $mold->setCompany($company);
        $mold->setProject($project);
        $mold->setNumProject($project->getNum());
        $mold->setNum($num);
        $mold->setName($form->get('name')->getData());
        $mold->setDescription($form->get('description')->getData());
        $mold->setDate(new DateTime);
        $mold->setSolution($form->get('solution')->getData());

        $em->persist($mold);                
        $em->flush();
    }
   
    public function editMold($id, $project, $form) {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Mold');
        
        $mold = $repository->find($id);
        $dateReg = $mold->getDate();
        
        $num1 = $form->get('num1')->getData();
        $das = $form->get('das')->getData();
        $num2 = $form->get('num2')->getData();
        
        $num = $num1 . '_' . $das . '_' .  $num2;
        
        $company = $project->getCompany();
        
        $mold->setCompany($company);
        $mold->setProject($project);
        $mold->setNumProject($project->getNum());
        $name = $form->get('name')->getData();
        $mold->setName($name);
        
        // Création du pathcode Affaire (2 derniers chiffres de l'année + id + 8 premières lettres du nom du projet)
        
        // On supprime les espaces du nom qu'on vient de récupérer
        $nameWs = str_replace(' ', '', $name);
        // Si la chaine obtenue fait moins de 8 caractères, on comble par des "X" à droite jusqu'à avoir une chaine de 8 caractères
        if(strlen($nameWs) < 8){
            $nameX = str_pad($nameWs, 8, "X", STR_PAD_RIGHT);
        }
        else{
            $nameX = $nameWs;
        }
        
        $chiffres = array("0","1","2","3","4","5","6","7","8","9");
        // On supprime les chiffres à gauche du nom obtenu
        $nameWf = ltrim($nameX, '0123456789');
        // On remplace les chiffres restants par desX et on met le nom en majuscule
        $newName = strtoupper(str_replace($chiffres, 'X', $nameWf));
        // On récupère les deux derniers chiffres de l'année
        $yy = date('y', $dateReg->getTimestamp());
        $chaine = $id . substr($newName, 0, 8);
        $pathcode = $yy . str_pad($chaine, 12, "0", STR_PAD_LEFT);
        
        $mold->setNum($num);
        $mold->setPathcode($pathcode);
        $mold->setDescription($form->get('description')->getData());
        $mold->setSolution($form->get('solution')->getData());
        
        $em->persist($mold);

        $em->flush();
        
    }
    
    // Suppression d'un projet
    public function deleteMold($id) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Mold');

        $mold = $repository->find($id);

        $em->remove($mold);

        $em->flush();
    }

    public function listMoldsAction($projectId){
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Mold');
        $repoProject = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project');
        
        $project = $repoProject->find($projectId);
        $molds = $repository->getNotArchivedByProject($project);//findBy(array('project' => $project));
        if(!$molds){
            return $this->redirect($this->generateUrl('asmolding_admin_manageMolds', array('projectId' => $projectId, 'id' => 0, 'mode' => 'ajouter')));
        }
        return $this->render('AsmoldingBundle:Admin:listeMolds.html.twig', array('molds' => $molds, 'projectId' => $projectId));
    }
    
    public function listArchivedMoldsAction($projectId){
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Mold');
        $repoProject = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Project');
        
        $project = $repoProject->find($projectId);
        $molds = $repository->getArchivedByProject($project);//findBy(array('project' => $project));
        if(!$molds){
            return $this->redirect($this->generateUrl('asmolding_admin_manageMolds', array('projectId' => $projectId, 'id' => 0, 'mode' => 'ajouter')));
        }
        return $this->render('AsmoldingBundle:Admin:listeArchivedMolds.html.twig', array('molds' => $molds, 'projectId' => $projectId));
    }
    
     // Création du pathcode affaire
    public function createMoldPathCode($form){
        
        // Création du pathcode (2 derniers chiffres de l'année + id + 8 premières lettres du nom du projet (sichiffres ==> X)
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Mold');
        
        $name = $form->get('name')->getData();
         
        $mold = $repository->findOneBy(array('name' => $name,));
        $dateReg = $mold->getDate();
        
        $idMold = $mold->getId();
        $nameWs = str_replace(' ', '', $name);
         // Si la chaine obtenue fait moins de 8 caractères, on comble par des "X" jusqu'à avoir une chaine de 8 caractères
        if(strlen($nameWs) < 8){
            $nameX = str_pad($nameWs, 8, "X", STR_PAD_RIGHT);
        }
        else{
            $nameX = $nameWs;
        }
        // Création du pathcode Affaire (2 derniers chiffres de l'année + id + 8 premières lettres du nom du projet)
        $chiffres = array("0","1","2","3","4","5","6","7","8","9");
        $nameWf = ltrim($nameX, '0123456789');
        //$nameWd = str_replace($chiffres, "X", $nameWf);
        $newName = strtoupper(str_replace($chiffres, 'X', $nameWf));
        $yy = date('y', $dateReg->getTimestamp());
        $chaine = $idMold . substr($newName, 0, 8);
        $pathcode = $yy . str_pad($chaine, 12, "0", STR_PAD_LEFT);
        
        $mold->setPathcode($pathcode);
        
        $em->persist($mold);                
        $em->flush();
    }
}
