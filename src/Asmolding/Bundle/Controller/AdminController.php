<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\Company;
use Asmolding\Bundle\Entity\CompanyRepository;
use Asmolding\Bundle\Entity\Contact;
use Asmolding\Bundle\Form\ChangePasswordType;
use Asmolding\Bundle\Form\FormCompanyType;
use Asmolding\Bundle\Form\FormContactType;
use Asmolding\Bundle\Form\FormSearchType;
use DateTime;
use Swift_Image;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of AdminController
 *
 * @author SGOURMELON
 */
class AdminController extends Controller{
    
     public function indexAction(){
        
        $user = $this->getUser();
        $prenom = $user->getFirstname();
        $nom = $user->getName();
        $name = $prenom . ' ' . $nom;
       
        return $this->render('AsmoldingBundle:Admin:accueilAdmin.html.twig', array('name' => $name));
    }
    
    public function manageProfileAction($id, $mode = "", Request $request){
        //creation d'un nouvel objet contact
        $contact = new Contact();
        
        // Récupération des infos du contact
        $contact = $em = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact')->find($id);
        if($contact){
            $company = $contact->getCompany();
        }
        // Construction du formulaire
        $form = $this->createForm(new FormContactType, $contact);
        
        if($mode == 'modifier'){
            if(!$contact->getLevel()){
                $form->remove('level');
            }if(!$contact->getCp()){
                $form->remove('cp');
            }
                $form->add('modifier', 'submit');
        }
        
        //Récupérer les données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Valider les données récupérées
        if($form->isValid()){
            
            if ($form->has('modifier')) {
                $this->editProfile($id, $company, $form);
            }
            
            //Retour vers la vue profil
            $response = $this->redirect($this->generateUrl('asmolding_admin_viewProfile', array('id' => $id)));
                    //$this->forward('AsmoldingBundle:Admin:viewProfile', array('id' => $id));
            return $response; 
        }
        
        //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Contact:profil.html.twig';
            return $this->render($template,array('form'=>$formView,'contact'=>$contact));
    }
    
    public function editProfile($id, $company, $form){
    
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
    
    public function manageContactsAction($id = 0, $mode = "", Request $request, $search = ""){
        
        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        //creation d'un nouvel objet contact
        $contact = new Contact();
        
        // Si l'id renseigné dans la requête est différent de 0, récupération du contact pour modification
        if($id !=0){
            $contact = $em = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact')->find($id);
        }
        if(!$contact){
                throw new HttpException(404, 'Ce contact n\'existe pas');
        }
        
        // Construction du formulaire
        $form = $this->createForm(new FormContactType, $contact);
        
        // si l'id = 0, on est en mode ajout, on crée un bouton "Ajouter"
        if ($id == 0 || $mode == "ajout") {
             if($search != ""){
                $form->remove('company');
                $form->add('company', 'entity', array(
                    'class' => 'AsmoldingBundle:Company',
                    'property' => 'name',
                    'empty_value' => 'Choisissez un client',
                    'constraints' => array(new NotBlank),
                    'query_builder' => function(CompanyRepository $er) use($search) {
                        return $er->searchCompanies($search);},));
                }
            $form->add('ajouter', 'submit');
        } 
        // sinon on crée un bouton "Modifier"
        else if($mode == "modifier") {
            $form->add('modifier', 'submit'); 
        }
        
        if($mode == "modifier" && $contact->getCompany()->getLevel() != true){
            $form->remove('level');
            $form->remove('cp');
        }
        
        //Récupération des données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Validation des données récupérées
        if($form->isValid()){
            
            if ($form->has('ajouter')) {
                //Appel de la méthode d'ajout
                $this->addContact($form);
                // Appel de la méthode d'envoi de mail
                //$this->sendEmail($contact);
            } else if ($form->has('modifier')) {
                // Appel de la méthode de modification
                $this->editContact($id, $form);
            } 
            
            //Retour vers la vue liste des utilisateurs
            $response = $this->redirect($this->generateUrl('asmolding_admin_listContacts'));
                    //$this->forward('AsmoldingBundle:Admin:listUsers');
            return $response; 
        }
        
        if($mode == 'supprimer'){
            
            $this->deleteContact($id);
            
            
            //Retour vers la vue liste des utilisateurs
            $response = $this->redirect($this->generateUrl('asmolding_admin_listContacts'));
                    //$this->forward('AsmoldingBundle:Admin:listUsers');
            return $response;
        }
        
        //envoi à la vue 
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Contact:contact.html.twig';
            return $this->render($template,array('form'=>$formView,'contact'=>$contact));
        
    }
    
    public function searchContactsAction(Request $request){
        
        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        $contact = new Contact();
        
        $form = $this->createForm(new FormSearchType, $contact);
        
        $form->add('company', 'text', array('constraints' => array(new NotBlank,)));
        $form->add('rechercher', 'submit');
        
        $form->handleRequest($request);
        
        
        if($form->isValid()){
            $name = $form->get('name')->getData();
            $companyName = $form->get('company')->getData();

            //Retour vers la vue liste de recherche
            if($name != ""){
                $response = $this->redirect($this->generateUrl('asmolding_admin_listFoundContactsByName', array('name' => $name)));
            }
            elseif ($name == "" && $companyName != ""){
                $response = $this->redirect($this->generateUrl('asmolding_admin_listFoundContactsByCompany', array('companyName' => $companyName)));
            }
            elseif ($name == "" && $companyName == ""){
                throw new HttpException(500, 'Au moins un champ doit être renseigné');   
            }
            return $response;
        } 
        
         //envoi à la vue recherche
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Search:search.html.twig';
            return $this->render($template,array('form'=>$formView,'contact'=>$contact));
         
    }
    
    public function addContact($form){
        
        $em = $this->getDoctrine()->getManager();
        
        $contact = new Contact(); 
        
        $contact->setCompany($form->get('company')->getData());
        // si l'utilisateur fait partie d'ASM, il peut obtenir des droits d'administration
        if($contact->getCompany()->getLevel() == true){
            if($form->get('level')->getData() == 'on'){
                $contact->setLevel(true);
            }
            else{
                $contact->setLevel(false);
            }
            if($form->get('cp')->getData() == 'on'){
                $contact->setCp(true);
            }
            else{
                $contact->setCp(false);
            }
        }
        else {
            $contact->setLevel(false);
        }
        $contact->setFirstname(str_replace(' ', '-', ucwords(str_replace('-',' ', $form->get('firstname')->getData()))));
        $contact->setName(mb_strtoupper($form->get('name')->getData()));
        $contact->setUsername($form->get('username')->getData());
        $contact->setPassword($this->generatePassword(8));
        $contact->setEmail($form->get('email')->getData());
        $contact->setPhone($form->get('phone')->getData());
        $contact->setFax($form->get('fax')->getData());
        $contact->setMobile($form->get('mobile')->getData());
        $contact->setPrivatePhone($form->get('privatePhone')->getData());
        $contact->setDepartment($form->get('department')->getData());
        $contact->setInsertBy('company');
        
        $em->persist($contact);                
        $em->flush();
        
        // Appel de la méthode d'envoi de mail
        $this->sendEmail($contact);
       
    }
    
    public function editContact($id, $form) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Contact');
        
        $contact = $repository->find($id);
        
        $contact->setCompany($form->get('company')->getData());
        if($contact->getCompany()->getLevel() == true){
            if($form->get('level')->getData() == 'on'){
                $contact->setLevel(true);
            }
            else{
                $contact->setLevel(false);
            }
            if($form->get('cp')->getData() == 'on'){
                $contact->setCp(true);
            }
            else{
                $contact->setCp(false);
            }
        }
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
    
    // Suppression d'un contact
    public function deleteContact($id) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Contact');

        $contact = $repository->find($id);

        $em->remove($contact);

        $em->flush();
    }
    
    // Liste des utilisateurs
    
    public function listContactsAction() {

        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact');

        $contacts = $repository->getContactsWithCompany();
        if(!$contacts){
            return new Response('Aucun contact existant. Crééez un contact');
        }

        return $this->render('AsmoldingBundle:Admin:listeContacts.html.twig', array('contacts' => $contacts));
    }
    
    public function listFoundContactsByNameAction($name) {

        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact');
        
        $foundContacts = $repository->searchContactsByName($name);
        
        if(!$foundContacts){
             throw new NotFoundHttpException('Aucun contact commençant par "' . $name . '" trouvé');  
        }
        return $this->render('AsmoldingBundle:Admin:listeFoundContacts.html.twig', array('foundContacts' => $foundContacts));
    }
    
    public function listFoundContactsByCompanyAction($companyName) {

        if(!$this->get('security.context')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès réservé aux administrateurs !');
        }
        
        $repository = $this->getDoctrine()->getManager()->getRepository('AsmoldingBundle:Contact');
        
        $foundContacts = $repository->searchContactsByCompanyName($companyName);
        
        if(!$foundContacts){
             throw new NotFoundHttpException('Aucun contact appartenant à la société commençant par "' . $companyName . '" trouvé');  
        }
        return $this->render('AsmoldingBundle:Admin:listeFoundContacts.html.twig', array('foundContacts' => $foundContacts, 'companyName' => $companyName));
    }
    
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
                $this->addCompany($form);
                // Appel de la méthode de création du patchcode client
                $this->createClientPathCode($form);
            } else if ($form->has('modifier')) {
                // appel de la méthode de modification
                $this->editCompany($id, $form);
            } 
            
            //Retour vers la vue liste des utilisateurs
            $response = $this->redirect($this->generateUrl('asmolding_admin_listCompanies'));
            return $response; 
        }
        
        if($mode == 'supprimer'){
            
            $this->deleteCompany($id);
            //$this->listUsersAction();
            
            //Retour vers la vue liste 
            $response = $this->redirect($this->generateUrl('asmolding_admin_listCompanies'));
                    //$this->forward('AsmoldingBundle:Admin:listCompanies');
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
                    //$this->forward('AsmoldingBundle:Admin:listCompanies');
            return $response;
        } 
        
         //envoi à la vue recherche
            $formView = $form->createView();
            $template= 'AsmoldingBundle:Search:search.html.twig';
            return $this->render($template,array('form'=>$formView,'company'=>$company));
         
    }
    
    public function addCompany($form){
        
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
        
        
        $company->setRegist(new DateTime);
       
        $em->persist($company);
        $em->flush();
    }
    
    public function editCompany($id, $form) {

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
    public function deleteCompany($id) {

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

    /*
     * FONCTION UTILITAIRES
     */
    
    // Création du pathcode Client
    public function createClientPathCode($form){
        
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
    
    // Génération du mot de passe aléatoire
    public function generatePassword($size){
        //Initialize the random password
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $password = '';
        for ($i = 0; $i < $size; $i++) {
            $password .= $characters[rand(0, $charactersLength - 1)];
        }
        /*
        for($length = 0; $length < $size; $length++) {
            //Append a random ASCII character (including symbols)
            $password .= chr(rand(32, 126));
        }*/

        return $password;
    }
    
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
          $response = $this->redirect($this->generateUrl('asmolding_admin_viewProfile', array('id' => $id)));
                  //$this->forward('AsmoldingBundle:Admin:viewProfile', array('id' => $id));
          return $response;
      }

      //envoi à la vue 
      $formView = $form->createView();
      $template= 'AsmoldingBundle:Contact:changePassword.html.twig';
      return $this->render($template,array('form'=>$formView));      
    }
    
    public function sendEmail($contact){  
       
        $recipientAddress = $contact->getEmail();
          
        $message = Swift_Message::newInstance();
        $cid = $message->embed(Swift_Image::fromPath('../web/images/logo_asm.png'));

        $message->setSubject('test mail');
        $message->setFrom('send@example.com');
        $message->setTo($recipientAddress);
        $message->setBody($this->renderView('AsmoldingBundle:Mail:mailNewContact.html.twig', array('contact' => $contact, 'cid' => $cid)), 'text/html');
        
    $this->get('mailer')->send($message);

    }

}
