<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Controller;

use Asmolding\Bundle\Entity\CompanyRepository;
use Asmolding\Bundle\Entity\Contact;
use Asmolding\Bundle\Form\Type\FormContactType;
use Asmolding\Bundle\Form\Type\FormSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of ContactController
 *
 * @author SGOURMELON
 */
class ContactController extends Controller{
    
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
        
        if($mode == "modifier" && $contact->getCompany()->getLevel() !== true){
            $form->remove('level');
            $form->remove('cp');
        }
        
        //Récupération des données du formulaire dans la requête HTTP
        $form->handleRequest($request);
        
        //Validation des données récupérées
        if($form->isValid()){
            
            if ($form->has('ajouter')) {
                //Appel de la méthode d'ajout
                $this->addContactAction($form);
            } else if ($form->has('modifier')) {
                // Appel de la méthode de modification
                $this->editContactAction($id, $form);
            } 
            
            //Retour vers la vue liste des utilisateurs
            $response = $this->redirect($this->generateUrl('asmolding_admin_listContacts'));
            return $response; 
        }
        
        if($mode == 'supprimer'){
            
            $this->deleteContactAction($id);

            //Retour vers la vue liste des utilisateurs
            $response = $this->redirect($this->generateUrl('asmolding_admin_listContacts'));
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
    
    public function addContactAction($form){
        
        $em = $this->getDoctrine()->getManager();
        
        $contact = new Contact(); 
        
        $contact->setCompany($form->get('company')->getData());
        // si l'utilisateur fait partie d'ASM, il peut obtenir des droits d'administration
        if($contact->getCompany()->getLevel() === true){
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
        $contact->setPassword($this->generatePasswordAction(8));
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
        $this->sendEmailAction($contact);
       
    }
    
    public function editContactAction($id, $form) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AsmoldingBundle:Contact');
        
        $contact = $repository->find($id);
        
        $contact->setCompany($form->get('company')->getData());
        if($contact->getCompany()->getLevel() === true){
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
    public function deleteContactAction($id) {

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
    
        // Génération du mot de passe aléatoire
    public function generatePasswordAction($size){
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
    
    public function sendEmailAction($contact){  
       
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
