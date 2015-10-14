<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\Company;
use Asmolding\Bundle\Form\Type\FormCompanyType;
use Asmolding\Bundle\Form\Type\FormSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Description of CompanyController
 *
 * @author SGOURMELON
 */
class CompanyController extends Controller{
    
        // Gestion des clients (companies)
     public function manageCompaniesAction($id = 0, $mode = "", Request $request){
        
        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        //creation d'un nouvel objet company
        $company = new Company();
        
        // Si l'id renseigné dans la requête est différent de 0, récupération de la company pour modification
        if($id !=0){
            $company = $em = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Company')->find($id);
        }
        if(!$company){
                throw new HttpException(404, 'Ce client n\'existe pas');
        }
        
        //creation du formulaire company
        $form = $this->createForm(new FormCompanyType(), $company);
            
            // si l'id = 0, on crée un bouton Ajouter
            if ($id == 0 || $mode == "ajout") {
                $form->add('ajouter', 'submit');
            } else {
                $form->add('modifier', 'submit'); // sinon on crée un bouton Modifier
            }
            
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
            
            if ($form->has('ajouter')) {
                //Appel de la méthode d'ajout
                $this->addCompanyAction($form);
                // Appel de la méthode de création du patchcode client
                $this->createClientPathCodeAction($form);
            } else if ($form->has('modifier')) {
                // appel de la méthode de modification
                $this->editCompanyAction($id, $form);
            } 
            
            //Retour vers la vue liste des utilisateurs
            $response = $this->redirect($this->generateUrl('asmolding_admin_listCompanies'));
            return $response; 
        }
        
        if($mode == 'supprimer'){
            
            $this->deleteCompanyAction($id);
            
            //Retour vers la vue liste 
            $response = $this->redirect($this->generateUrl('asmolding_admin_listCompanies'));
            return $response;
        }
        
        //envoi à la vue des clients
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Company:company.html.twig';
            return $this->render($template,array('form'=>$formView,'company'=>$company));
        
    }
    
    public function searchCompaniesAction(Request $request){
        
        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        $company = new Company();

        $form = $this->createForm(new FormSearchType, $company);
        
        $form->add('rechercher', 'submit');
        
        $form->handleRequest($request);
        

        if($form->isValid()){
            $name = $form->get('name')->getData();
            
            //Retour vers la vue liste de recherche
            $response = $this->redirect($this->generateUrl('asmolding_admin_listFoundCompanies', array('name' => $name)));
            return $response;
        } 
        
         //envoi à la vue recherche
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Search:search.html.twig';
            return $this->render($template,array('form'=>$formView,'company'=>$company));
         
    }
    
    public function addCompanyAction($form){
        
        $em = $this->getDoctrine()->getManager();
        
        $company = new Company(); 

        $company->setName(mb_strtoupper($form->get('name')->getData()));
        $company->setName2(mb_strtoupper($form->get('name2')->getData()));
        $company->setLegalform(mb_strtoupper($form->get('legalform')->getData()));
        $company->setLegalform2(mb_strtoupper($form->get('legalform2')->getData()));
        $company->setCountry($form->get('country')->getData());
        $company->setCountry2($form->get('country2')->getData());
        $company->setAddress($form->get('address')->getData());
        $company->setAddress2($form->get('address2')->getData());
        $company->setZip($form->get('zip')->getData());
        $company->setZip2($form->get('zip2')->getData());        
        $company->setCity(mb_strtoupper($form->get('city')->getData()));
        $company->setCity2(mb_strtoupper($form->get('city2')->getData())); 
        $company->setPhone($form->get('phone')->getData());
        $company->setPhone2($form->get('phone2')->getData());
        $company->setFax($form->get('fax')->getData());
        $company->setFax2($form->get('fax2')->getData());
        $company->setEmail($form->get('email')->getData());
        $company->setEmail2($form->get('email2')->getData());
        $company->setWww($form->get('www')->getData());
        $company->setWww2($form->get('www2')->getData());
        $company->setBranche($form->get('branche')->getData());

        if($form->get('level')->getData() == 'on'){
            $company->setLevel(true);
        }
        else{
            $company->setLevel(false);
        }
        
        if($form->get('reglevel')->getData() == 'on'){
            $company->setReglevel(true);
        }
        else{
            $company->setReglevel(false);
        }
        
        
        $company->setRegist(new \DateTime);
       
        $em->persist($company);
        $em->flush();
    }
    
    public function editCompanyAction($id, $form) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Company');
        
        $company = $repository->find($id);

        $name = mb_strtoupper($form->get('name')->getData());
        $company->setName($name);
        $nameWs = str_replace(' ', '',$name);
        if(strlen($nameWs) < 4){
            $newName = str_pad($nameWs, 4, "X", STR_PAD_RIGHT);
        }
        else{
            $newName = str_replace(' ', '',$name);
        }
        // Création du pathcode (id + 4 premières lettres du nom du client)
        $chaine = $id . substr($newName, 0, 4);
        $pathcode = str_pad($chaine, 8, "0", STR_PAD_LEFT);
        
        $company->setPathcode($pathcode);
        $company->setName2(mb_strtoupper($form->get('name2')->getData()));
        $company->setLegalform(mb_strtoupper($form->get('legalform')->getData()));
        $company->setLegalform2(mb_strtoupper($form->get('legalform2')->getData()));
        $company->setCountry($form->get('country')->getData());
        $company->setCountry2($form->get('country2')->getData());
        $company->setAddress($form->get('address')->getData());
        $company->setAddress2($form->get('address2')->getData());
        $company->setZip($form->get('zip')->getData());
        $company->setZip2($form->get('zip2')->getData());        
        $company->setCity(mb_strtoupper($form->get('city')->getData()));
        $company->setCity2(mb_strtoupper($form->get('city2')->getData())); 
        $company->setPhone($form->get('phone')->getData());
        $company->setPhone2($form->get('phone2')->getData());
        $company->setFax($form->get('fax')->getData());
        $company->setFax2($form->get('fax2')->getData());
        $company->setEmail($form->get('email')->getData());
        $company->setEmail2($form->get('email2')->getData());
        $company->setWww($form->get('www')->getData());
        $company->setWww2($form->get('www2')->getData());
        $company->setBranche($form->get('branche')->getData());

        if($form->get('level')->getData() == 'on'){
            $company->setLevel(true);
        }
        else{
            $company->setLevel(false);
        }
        
        if($form->get('reglevel')->getData() == 'on'){
            $company->setReglevel(true);
        }
        else{
            $company->setReglevel(false);
        }

        $em->persist($company);

        $em->flush();
    }
    
    // Suppression d'un client
    public function deleteCompanyAction($id) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Company');

        $company = $repository->find($id);

        $em->remove($company);

        $em->flush();
    }
  
    public function listCompaniesAction() {

        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Company');

        $companies = $repository->findAll();
        if(!$companies){
            return new Response('Aucun client existant. Crééez un client');
        }
        
        foreach ($companies as $company){
            $companyName = $company->getName();
            $contacts = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact')->searchContactsByCompanyName($companyName);
        }
        
        return $this->render('AsmoldingBundle:Admin:listeCompanies.html.twig', array('companies' => $companies, 'contacts' => $contacts));
    }
    
    public function listFoundCompaniesAction($name) {

        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Company');

        $foundCompanies = $repository->searchCompaniesByName($name);
        if(!$foundCompanies){
            throw new NotFoundHttpException('Aucun client commençant par "' . $name . '" trouvé');
        }
        return $this->render('AsmoldingBundle:Admin:listeFoundCompanies.html.twig', array('foundCompanies' => $foundCompanies, 'name' => $name));
    }
    
     // Création du pathcode Client
    public function createClientPathCodeAction($form){
        
        // Création du pathcode (id + 4 premières lettres du nom du client)
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Company');
        
        $name = $form->get('name')->getData();
        
        $company = $repository->findOneBy(array('name' => $name,));
        
        $idCmpny = $company->getId();
        
        // Suppression des espaces dans la chaine
        $nameWs = str_replace(' ', '',$name);
        // Si la chaine obtenue fait moins de 4 caractères, on comble par des "X" jusqu'à avoir une chaine de 4 caractères
        if(strlen($nameWs) < 4){
            $newName = str_pad($nameWs, 4, "X", STR_PAD_RIGHT);
        }
        else{
            $newName = $nameWs;
        }
        // Création du pathcode (id + 4 premières lettres du nom du client)
        $chaine = $idCmpny . substr($newName, 0, 4);
        // On comble avec des "0" pour obtenir une chaine de 8 caractères
        $pathcode = str_pad($chaine, 8, "0", STR_PAD_LEFT);
        
        $company->setPathcode($pathcode);
        
        $em->persist($company);                
        $em->flush();
    }
}
