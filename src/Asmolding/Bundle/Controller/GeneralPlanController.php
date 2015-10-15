<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\GeneralPlan;
use Asmolding\Bundle\Entity\Milestone;
use Asmolding\Bundle\Entity\MilestoneRepository;
use Asmolding\Bundle\Form\Type\FormGeneralPlanType;
use Asmolding\Bundle\Form\Type\FormMilestoneType;
use DateTime;
use Html2Pdf_Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Description of GeneralPlanController
 *
 * @author SGOURMELON
 */
class GeneralPlanController extends Controller{
    
    public function generalPlanAction(){
        
        $em = $this->getDoctrine()->getManager();
        
        $controllerFull = strstr($this->getRequest()->get('_controller'), ':', 1) ;
        $pos = strrpos($controllerFull, '\\');
        $controller = substr(substr($controllerFull, $pos), 1);     
       
        $molds = $em->getRepository('AsmoldingBundle:Mold')->getAllNotArchived();
        
        $milestones = $em->getRepository('AsmoldingBundle:Milestone')->getAll();    
        
        $generalPlans = $em->getRepository('AsmoldingBundle:GeneralPlan')->getAll();
        
        
        $lastUpdate = $em->getRepository('AsmoldingBundle:GeneralPlan')->getRecentUpdateTime();
        
        $today = date('d-m-Y');
        $numWeek = date("W", strtotime($today));
        $year = date("Y", strtotime($today));
        
        return $this->render('AsmoldingBundle:Admin:generalPlan.html.twig', array('molds' => $molds, 'generalPlans' => $generalPlans,'milestones' => $milestones,'numWeek' => $numWeek, 'year' => $year, 'lastUpdate' => $lastUpdate, 'controller' => $controller));
    }
    
    public function generalPlanCPAction(){
        
        $em = $this->getDoctrine()->getManager();
        
        $controllerFull = strstr($this->getRequest()->get('_controller'), ':', 1) ;
        $pos = strrpos($controllerFull, '\\');
        $controller = substr(substr($controllerFull, $pos), 1);
        
        if($this->get('security.context')->isGranted('ROLE_CHEFPROJET')){
            $cp = $this->getUser();
            $molds = $em->getRepository('AsmoldingBundle:Mold')->getNotArchivedMoldsByCP($cp); 
        }

        $milestones = $em->getRepository('AsmoldingBundle:Milestone')->getAll();    
        
        $generalPlans = $em->getRepository('AsmoldingBundle:GeneralPlan')->getAll();
        
        
        $lastUpdate = $em->getRepository('AsmoldingBundle:GeneralPlan')->getRecentUpdateTime();
        
        $today = date('d-m-Y');
        $numWeek = date("W", strtotime($today));
        $year = date("Y", strtotime($today));
        
        return $this->render('AsmoldingBundle:Admin:generalPlan.html.twig', array('molds' => $molds, 'generalPlans' => $generalPlans,'milestones' => $milestones,'numWeek' => $numWeek, 'year' => $year, 'lastUpdate' => $lastUpdate, 'controller' => $controller));
    }
    
    public function manageGeneralPlanAction($id, Request $request){
        
        $em = $this->getDoctrine()->getManager();
        $mold = $em->getRepository('AsmoldingBundle:Mold')->find($id);
        $project = $mold->getProject();
        if(strlen($mold->getNum()) == 11){
            $das = substr($mold->getNum(), 4, 3);
        }else{
            $das = substr($mold->getNum(), 4, 2);
        }

        $generalPlan = $em->getRepository('AsmoldingBundle:GlobalPlan')->findOneByMold($mold);
        
        //creation du formulaire GeneralPlan
        $form = $this->createForm(new FormGeneralPlanType, $generalPlan);
        
        $form->add('milestone', 'entity', array(
          'class' => 'AsmoldingBundle:Milestone',
          'property' => 'name',
          'query_builder' => function(MilestoneRepository $er) use($das){
                        return $er->getOrderedByDas($das);}));

        
        $form->add('valider', 'submit');
        $form->get('project')->setData($project);
        $form->get('mold')->setData($mold);
        $form->add('test', 'hidden');
        $form->get('test')->setData('all');
        
            
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid() /*&& $form2->isValid()*/){
            
            if ($form->has('valider')) {
                
                $generalPlan = new GeneralPlan(); 
                $user = $this->getUser();

                $prevWeek = $form->get('pweek')->getData();    
                $prevDay = $form->get('pday')->getData();
                $prevYear = $form->get('pyear')->getData();
                if($prevWeek && $prevDay && $prevYear){
                    $previsionnel = new \DateTime($this->findDateAction($prevWeek, $prevDay, $prevYear));       
                }else {
                    $previsionnel = null;
                }

                $realWeek = $form->get('rweek')->getData();    
                $realDay = $form->get('rday')->getData();
                $realYear = $form->get('ryear')->getData();
                if($realWeek && $realDay && $realYear){
                    $reel = new \DateTime($this->findDateAction($realWeek, $realDay, $realYear));
                }else {
                    $reel = null;
                }

                $generalPlan->setProject($project);
                $generalPlan->setMold($mold);
                $generalPlan->setMilestone($form->get('milestone')->getData());
                $generalPlan->setPrevisionnel($previsionnel);
                $generalPlan->setReel($reel);
                $generalPlan->setUpdateTime(new DateTime);
                $generalPlan->setUpdateUser($user);

                $em->persist($generalPlan);                
                $em->flush();
            }
            
            /*
            //Retour vers la vue liste des projets
            $response = $this->redirect($this->generateUrl('asmolding_admin_generalPlan', array('generalPlan' => $generalPlan)));
            return $response;
             */
        } 
            //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:GlobalPlan:globalPlan.html.twig';
            return $this->render($template,array('form'=>$formView, 'generalPlan'=>$generalPlan));  
    }
    
    public function editGeneralPlanAction($moldId, $milestoneId, $type, Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        $todayYear = date('Y');
        
        $mold = $em->getRepository('AsmoldingBundle:Mold')->find($moldId);
        $project = $mold->getProject();
        $milestone = $em->getRepository('AsmoldingBundle:Milestone')->find($milestoneId);
        
        $generalPlan = $em->getRepository('AsmoldingBundle:GeneralPlan')->getOneByMoldAndMilestone($mold, $milestone);
        
        $user = $this->getUser();
        
        if(!$generalPlan){
            $generalPlan = new GeneralPlan();
            $generalPlan->setPrevisionnel('1.1.' . $todayYear);
            $generalPlan->setReel('1.1.' . $todayYear);
        }
        
        //creation du formulaire GeneralPlan
        $form = $this->createForm(new FormGeneralPlanType, $generalPlan);
        
        $prev = $generalPlan->getPrevisionnel();
        $real = $generalPlan->getReel();
        
        if($prev !== null){$prevDate = $prev->format('W.N.o');}
        else{$prevDate = '1.1' . '.' . $todayYear;}
        if($real !== null){$realDate = $real->format('W.N.o');}
        else{$realDate = '1.1' . '.' . $todayYear;}
        
        
        if($type == 'previsionnel'){
            if(strlen(explode('.', $prevDate)[0]) == 1){
                $pweek = substr($prevDate, 0, 1);
                $pday = substr($prevDate, 2, 1);
                $pyear = substr($prevDate, 4, 4);
            }else{
                $pweek = substr($prevDate, 0, 2);
                $pday = substr($prevDate, 3, 1);
                $pyear = substr($prevDate, 5, 4);
            }              
        }else{
            if(strlen(explode('.', $realDate)[0]) == 1){
                $rweek = substr($realDate, 0, 1);
                $rday = substr($realDate, 2, 1);
                $ryear = substr($realDate, 4, 4);
            }else{
                $rweek = substr($realDate, 0, 2);
                $rday = substr($realDate, 3, 1);
                $ryear = substr($realDate, 5, 4);
            }
            
        }

            $form->add('valider', 'submit'); // sinon on crée un bouton Modifier
            $form->add('test', 'hidden');
            $form->get('test')->setData($type);
            $form->get('project')->setData($project);
            $form->get('mold')->setData($mold);
            $form->get('milestone')->setData($milestone);
            if($type == 'previsionnel'){
               $form->remove('rweek');
               $form->remove('rday');
               $form->remove('ryear');
               $form->get('pweek')->setData($pweek);
               $form->get('pday')->setData($pday);
               $form->get('pyear')->setData($pyear);
            }else{
               $form->remove('pweek');
               $form->remove('pday');
               $form->remove('pyear');
               $form->get('rweek')->setData($rweek);
               $form->get('rday')->setData($rday);
               $form->get('ryear')->setData($ryear);
            }
            
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
        
        $generalPlan->setProject($project);
        $generalPlan->setMold($mold);
        $generalPlan->setMilestone($milestone);
        
        if($type == 'previsionnel'){
            $prevWeek = $form->get('pweek')->getData();    
            $prevDay = $form->get('pday')->getData();
            $prevYear = $form->get('pyear')->getData();
            $previsionnel = new \DateTime($this->findDateAction($prevWeek, $prevDay, $prevYear));
            $generalPlan->setPrevisionnel($previsionnel);
        }
        else{
            $realWeek = $form->get('rweek')->getData();    
            $realDay = $form->get('rday')->getData();
            $realYear = $form->get('ryear')->getData();
            $reel = new \DateTime($this->findDateAction($realWeek, $realDay, $realYear));
            $generalPlan->setReel($reel);
        }
        
        $generalPlan->setUpdateTime(new DateTime);
        $generalPlan->setUpdateUser($user);
    
        $em->persist($generalPlan);
        $em->flush();
        
        //Retour vers la vue planning général
            $response = $this->redirect($this->generateUrl('asmolding_admin_generalPlan', array('generalPlan' => $generalPlan)));
            return $response;
        }

            //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:GlobalPlan:globalPlan.html.twig';
            return $this->render($template,array('form'=>$formView,'generalPlan'=>$generalPlan));
   
    }
    
    
    public function manageMilestonesAction($id=0, Request $request){
        
        $em = $this->getDoctrine()->getManager();
        $milestone = new Milestone();
        
        if($id !=0){
            $milestone = $em->getRepository('AsmoldingBundle:Milestone')->find($id);
        }
        if(!$milestone){
                throw new HttpException(404, 'Ce jalon n\'existe pas');
        }
        
         //creation du formulaire milestone
        $form = $this->createForm(new FormMilestoneType, $milestone);
        
        if ($id == 0) {
            $form->add('ajouter', 'submit');
        } else {
            $form->add('modifier', 'submit'); 
        }
        
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
           
             if ($form->has('ajouter')) {
                //Appel de la méthode d'ajout
                $this->addMilestoneAction($form);
            } else if ($form->has('modifier')) {
                // Appel de la méthode de modification
                $this->editMilestoneAction($id, $form);
            }
                
            
            
            //Retour vers la vue liste des jalons
            $response = $this->redirect($this->generateUrl('asmolding_admin_listMilestones'));
            return $response;
        }
    
            //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:GlobalPlan:milestone.html.twig';
            return $this->render($template,array('form'=>$formView,'milestone'=>$milestone));  
    }
    
    public function addMilestoneAction($form){
        
        $em = $this->getDoctrine()->getManager();
        $milestone = new Milestone();
        
        $milestone->setName($form->get('name')->getData());
        $milestone->setDas($form->get('das')->getData());
        $milestone->setSolution($form->get('solution')->getData());
        $milestone->setSequence($form->get('sequence')->getData());
        if($form->get('visible')->getData() == 'on'){
            $milestone->setVisible(true);
        }else{
            $milestone->setVisible(false);
        }

        $em->persist($milestone);
        $em->flush();
    }
    
    public function editMilestoneAction($id, $form){
        
        $em = $this->getDoctrine()->getManager();
        
        $milestone = $em->getRepository('AsmoldingBundle:Milestone')->find($id);
        
        $milestone->setName($form->get('name')->getData());
        $milestone->setDas($form->get('das')->getData());
        $milestone->setSolution($form->get('solution')->getData());
        $milestone->setSequence($form->get('sequence')->getData());
        if($form->get('visible')->getData() == 'on'){
            $milestone->setVisible(true);
        }else{
            $milestone->setVisible(false);
        }

        $em->persist($milestone);
        $em->flush();
    }
    
    
    public function listMilestonesAction(){
        $em = $this->getDoctrine()->getManager();
        
        $milestones = $em->getRepository('AsmoldingBundle:Milestone')->getAll();
        
        $formDas = $this->createFormBuilder()
            ->add('das', 'choice', array('choices' => array
            ('MA' =>'MA',
             'MN' =>'MN',
             'MO' =>'MO',
             'PR' =>'PR',
             'PS' =>'PS',
             'VPI' =>'VPI',
             'VPU' =>'VPU',)))
            ->add('solution', 'choice', array('choices' => array
            ('FRANCE' =>'FRANCE',
             'FRANCO-CHINE' =>'FRANCO-CHINE',
             'CHINE' =>'CHINE',
             'DIRECT CHINE' => 'DIRECT CHINE')))
            ->getForm();
        
        $formDasView = $formDas->createView();
        
        return $this->render('AsmoldingBundle:Admin:listeMilestones.html.twig', array('formDas' => $formDasView, 'milestones' => $milestones));
    }
    
     public function printAction(){
    //on stocke la vue à convertir en PDF, en n'oubliant pas les paramètres twig si la vue comporte des données dynamiques
       
        $em = $this->getDoctrine()->getManager();
        
        if($this->get('security.context')->isGranted('ROLE_CHEFPROJET')){
            $cp = $this->getUser();
            $molds = $em->getRepository('AsmoldingBundle:Mold')->getMoldsByCP($cp);
        }
        else{
            $molds = $em->getRepository('AsmoldingBundle:Mold')->getAll();
        }
        
        $milestones = $em->getRepository('AsmoldingBundle:Milestone')->getAll();    
        
        $generalPlans = $em->getRepository('AsmoldingBundle:GeneralPlan')->getAll();

        $today = date('d-m-Y');
        $numWeek = date("W", strtotime($today));
        $year = date("Y", strtotime($today));
        
        $html = $this->renderView('AsmoldingBundle:Files:generalPlanPdf.html.twig', array('molds' => $molds, 'milestones' => $milestones, 'generalPlans' => $generalPlans, 'numWeek' => $numWeek, 'year' => $year));
        
        //on instancie la classe Html2Pdf_Html2Pdf en lui passant en paramètre
        //le sens de la page "portrait" => p ou "paysage" => l
        //le format A4,A5...
        //la langue du document fr,en,it...
        $html2pdf = new Html2Pdf_Html2Pdf('l','A3','fr');
 
        //SetDisplayMode définit la manière dont le document PDF va être affiché par l’utilisateur
        //fullpage : affiche la page entière sur l'écran
        //fullwidth : utilise la largeur maximum de la fenêtre
        //real : utilise la taille réelle
        $html2pdf->pdf->SetDisplayMode('real');
 
        //writeHTML va tout simplement prendre la vue stocker dans la variable $html pour la convertir en format PDF
        $html2pdf->writeHTML($html);
 
        //Output envoie le document PDF au navigateur internet avec un nom spécifique qui aura un rapport avec le contenu à convertir (exemple : Facture, Règlement…)
        $html2pdf->Output('Planning_General_Semaine_'.$numWeek.'_'.$year.'.pdf', 'D');

        
        $response = new Response('http://asmolding.local/file/Planning_General_Semaine_'.$numWeek.'_'.$year.'.pdf', 200, array('content-type' => 'application/pdf', 'Pragma' => 'private', 'Cache-control' => 'private, must-revalidate'));
        return $response;
    }
    
    // UTIL
    
    function findDateAction($semaine, $jour, $annee)
    {
        
        if(strftime("%V",mktime(0,0,0,01,01,$annee)) == 1 || $semaine == 1 && strftime("%V",mktime(0,0,0,01,01,$annee)) != 1 && date("L", mktime(0,0,0,1,1,$annee+1))== 0){
            $mktime = mktime(0,0,0,01,(01+(($semaine)*7)),$annee);
        }
        else if(strftime("%V",mktime(0,0,0,01,01,$annee)) != 1 && $semaine == 1 && strftime("%V",mktime(0,0,0,01,01,$annee)) != 1 && date("L", mktime(0,0,0,1,1,$annee+1))== 1){
            $mktime = mktime(0,0,0,01,(01+(($semaine-1)*7)),$annee);
        }
        else if (strftime("%V",mktime(0,0,0,01,01,$annee)) == 0 && ($semaine >= 2 && $semaine < 53) && date("L", mktime(0,0,0,1,1,$annee+1))== 1){
            $mktime = mktime(0,0,0,01,(01+(($semaine-1)*7)),$annee);
        }
        else if (strftime("%V",mktime(0,0,0,01,01,$annee)) == 0 && ($semaine >= 2 && $semaine < 53)){
            $mktime = mktime(0,0,0,01,(01+(($semaine)*7)),$annee);
        }
        else if(strftime("%V",mktime(0,0,0,01,01,$annee)) == 0 && $semaine == 53){
            $mktime = mktime(0,0,0,01,(01+(($semaine-1)*7)),$annee);
        }

        if(date("N",$mktime) > 1){
            $decalage = ((date("N",$mktime)-1)*60*60*24);
        } else if (date("N", $mktime) == 1){
            $decalage = ((date("N",$mktime)+6)*60*60*24);
        }

        $lundi = $mktime - $decalage;
        $mardi = $lundi + (1*60*60*24);
        $mercredi = $lundi + (2*60*60*24);
        $jeudi = $lundi + (3*60*60*24);
        $vendredi = $lundi + (4*60*60*24);
        $samedi = $lundi + (5*60*60*24);
        $dimanche = $lundi + (6*60*60*24);

        $resultat = array("", 
                  date("m/d/Y",$lundi),
                  date("m/d/Y",$mardi),
                  date("m/d/Y",$mercredi),
                  date("m/d/Y",$jeudi),
                  date("m/d/Y",$vendredi),
                  date("m/d/Y",$samedi),
                  date("m/d/Y",$dimanche));

        $jours = array(0, 1, 2, 3, 4, 5, 6, 7);
        if(in_array($jour, $jours)){
            return $resultat[$jour];
        }
    }  
}
