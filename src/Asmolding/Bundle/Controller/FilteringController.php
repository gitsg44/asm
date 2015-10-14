<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Form\Type\FormFilterMoldPlanType;
use Asmolding\Bundle\Form\Type\FormFilterProjectPlanType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Description of FlteringController
 *
 * @author SGOURMELON
 */
class FilteringController extends Controller{
    
    public function filteringAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();

        $molds = $em->getRepository('AsmoldingBundle:Mold')->getAll();
        $projects = $em->getRepository('AsmoldingBundle:Project')->getAll();
        
        //$generalPlan = $em->getRepository('AsmoldingBundle:GlobalPlan')->findOneByMold($mold);
        
        foreach ($projects as $project){
            //creation du formulaire GeneralPlan
            $form = $this->createForm(new FormFilterProjectPlanType(), $project);  

            //Récupérer les données du formulaire dans la requête HTTP
            $form->handleRequest($request);

            //Valider les données récupérées
            if($form->isValid()){

            } 

            foreach ($molds as $mold){
                //creation du formulaire GeneralPlan
                $form2 = $this->createForm(new FormFilterMoldPlanType(), $mold); 

                //Récupérer les données du formulaire dans la requête HTTP
                $form2->handleRequest($request);

                //Valider les données récupérées
                if($form2->isValid()){

                } 
                    //envoi à la vue 
                    $formView = $form->createView();
                    $formView2 = $form2->createView();
                    $template= 'AsmoldingBundle:Admin:filters.html.twig';
                    return $this->render($template,array('form'=>$formView, 'form2' =>$formView2));

            }
        }
    }
    
    
    public function filteredAction($companyId = '', $contactId = '', $projectId = '', $das = '', $solution = '') {
        
        
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        $controllerFull = strstr($this->getRequest()->get('_controller'), ':', 1) ;
        $pos = strrpos($controllerFull, '\\');
        $controller = substr(substr($controllerFull, $pos), 1);
        
        if($request->isXmlHttpRequest()){
            if($companyId != ''){

                $company = $em->getRepository('AsmoldingBundle:Company')->find($companyId);
                
                if($this->get('security.context')->isGranted('ROLE_CHEFPROJET')){
                    $cp = $this->getUser();
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByCPAndByClient($cp, $company); 
                }
                else{
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByClient($company);
                }  
            } else if($contactId != ''){

                if($this->get('security.context')->isGranted('ROLE_CHEFPROJET')){
                    $cp = $this->getUser();
                } else{
                    $cp = $em->getRepository('AsmoldingBundle:Contact')->find($contactId);
                }
                $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedMoldsByCP($cp);  
            } else if($projectId != ''){

                $project = $em->getRepository('AsmoldingBundle:project')->find($projectId);
                
                if($this->get('security.context')->isGranted('ROLE_CHEFPROJET')){
                    $cp = $this->getUser();
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByCPAndByProject($cp, $project);
                } else{
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByProject($project);   
                }
            } else if($das != ''){

                if($this->get('security.context')->isGranted('ROLE_CHEFPROJET')){
                    $cp = $this->getUser();
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByCPAndByDas($cp, $das);
                } else{
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByDas($das);
                }
            } else if($solution != ''){

                if($this->get('security.context')->isGranted('ROLE_CHEFPROJET')){
                    $cp = $this->getUser();
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedByCPAndBySolution($cp, $solution);
                } else{
                    $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedBySolution($solution);
                }
            }

            
            $milestones = $em->getRepository('AsmoldingBundle:Milestone')->getAll();    

            $generalPlans = $em->getRepository('AsmoldingBundle:GeneralPlan')->getAll();

            if($companyId != ''){
                $lastUpdate = $em->getRepository('AsmoldingBundle:GeneralPlan')->getRecentUpdateTimeByClient($company);     
            } else if($projectId != ''){
                $lastUpdate = $em->getRepository('AsmoldingBundle:GeneralPlan')->getRecentUpdateTimeByProject($project);
            }
            else{
                $lastUpdate = $em->getRepository('AsmoldingBundle:GeneralPlan')->getRecentUpdateTime();
            }

            if(!$molds){
                throw new NotFoundHttpException('Aucun planning pour cette recherche');
            }

            $today = date('d-m-Y');
            $numWeek = date("W", strtotime($today));
            $year = date("Y", strtotime($today));

            return $this->render('AsmoldingBundle:Admin:generalPlan.html.twig', array('molds' => $molds, 'generalPlans' => $generalPlans,'milestones' => $milestones,'numWeek' => $numWeek, 'year' => $year, 'lastUpdate' => $lastUpdate, 'controller' => $controller));
    
        } else{
            return null;
        }
}
    /*
    public function searchCompaniesAjaxAction(Request $request){
        
        //$request = $this->getRequest();
        
        //var_dump($request->request->get('data'));
        
        if($request->isXmlHttpRequest()){

            $search = '';
            $search = $request->request->get('motcle');
            var_dump($search);

            $em = $this->getDoctrine()->getManager();

            if($search != null)
            {
                $companies = $em->getRepository('AsmoldingBundle:Company')->searchCompaniesByName($search);
            }
            else {
                $companies = $em->getRepository('AsmoldingBundle:Company')->getAll();
            }

            var_dump($companies);
            $template = 'AsmoldingBundle:Admin:filters.html.twig';
            
            return $this->render($template,array('formCompany'=>null, 'formContact' =>null, 'companies' => $companies));
        }
        else {
            return $this->filteringAction();
        }

    }*/
}
