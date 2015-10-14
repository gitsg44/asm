<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity; 
 

use Doctrine\ORM\Mapping as ORM; // Object-Relationnal Mapping - Mappe les entités avec la base de données
//
// Contraintes de validation et de sécurité du formulaire
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Description of Contact
 * 
 * Référence les contacts (clients, partenaires, chefs de projet ou administrateurs) (personne physique)
 *
 * @author SGOURMELON
 */
 
 /**
 * Contact
 *
 * @ORM\Table(name="CONTACTS")
 * @ORM\Entity(repositoryClass="Asmolding\Bundle\Entity\ContactRepository")
 */
class Contact implements UserInterface{
    
     /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Le nom d'utilisateur doit être renseigné")
     * @ORM\Column(name="username", type="string", length=40)
     */
    private $username;
    
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Le mot de passe doit être renseigné", groups={"changePassword"})
     * @Assert\Length(min=6, minMessage="Le mot de passe doit faire au moins {{ limit }} caractères", groups={"changePassword"})
     * @ORM\Column(name="pass", type="string", length=15)
     */
    private $password;
    
    /* 
     * Ancien mot de passe pour vérification de sécurité 
     * lors de la modification du mot de passe par l'utilisateur
     */
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Le mot de passe est erroné / doit être renseigné",
     *     groups = {"changePassword"}
     * )
     */
    private $oldPassword;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     * @Assert\NotBlank(message="Le nom du contact doit être renseigné")
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=35)
     * @Assert\NotBlank(message="Le prénom du contact doit être renseigné")
     */
    private $firstname;
    
    private $nameFirstname;
    
    private $initials;
    
    /**
     *
     * @var Company
     * 
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="contacts")
     */
    private $company;

    /**
     * @var boolean
     *
     * @ORM\Column(name="level", type="boolean")
     */
    private $level;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="chefProjet", type="boolean")
     */
    private $cp;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50)
     * @Assert\NotBlank(message="L'adresse mail doit être renseignée", groups="Default")
     * @Assert\Email(message="'{{ value }}' n'est pas une adresse mail valide")
     */
    private $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=35, nullable=true)
     */
    private $phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=35, nullable=true)
     */
    private $fax;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=35, nullable=true)
     */
    private $mobile;
    
    /**
     * @var string
     *
     * @ORM\Column(name="privatphone", type="string", length=35, nullable=true)
     */
    private $privatePhone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=50, nullable=true)
     */
    private $department;
    
    /**
     * @var string
     *
     * @ORM\Column(name="insertby", type="string", length=10)
     */
    private $insertBy;
    
    // Clés étrangères (FK)
    
    /**
     *
     * @var Project
     * 
     * @ORM\OneToMany(targetEntity="Project", mappedBy="cp") 
     */
    private $projectsCP;
    
    /**
     *
     * @var ProjetRelContact
     * 
     * @ORM\OneToMany(targetEntity="ProjetRelContact", mappedBy="contact", cascade={"remove"})
     *      
     */
    private $projetRelContacts;
    
    /**
     *
     * @var GeneralPlan
     * 
     * @ORM\OneToMany(targetEntity="GeneralPlan", mappedBy="updateUser")
     *      
     */
    private $generalPlans;

    // SECURITE
    
    public function eraseCredentials() {
        
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        $roles =  array();
        
        if($this->level){
            $roles[] = 'ROLE_ADMIN';
        }
        else if ($this->cp && $this->company->getLevel()){
            $roles[] = 'ROLE_CHEFPROJET';
        }
        else if (!$this->level && !$this->company->getLevel() && $this->company->getReglevel() == true){
            $roles[] = 'ROLE_PARTENAIRE';
        }
        else{
            $roles[] = 'ROLE_CLIENT';
        }
        
        return $roles;
    }

    public function getSalt() {
        return null;
    }

    public function getUsername() {
        return $this->username;
    }
    
    function getOldPassword() {
        return $this->oldPassword;
    }

    // Getters
    
    function getId() {
        return $this->id;
    }
    
    function getName() {
        return $this->name;
    }

    function getFirstname() {
        return $this->firstname;
    }

    function getNameFirstname(){
        return $this->name . ' ' . $this->firstname;
    }
    
    function getInitials() {
        return $this->initiales($this->firstname . ' ' . $this->name);
    }
    
    function getCompany() {
        return $this->company;
    }

    function getLevel() {
        return $this->level;
    }

    function getCp() {
        return $this->cp;
    }
    
    function getEmail() {
        return $this->email;
    }

    function getPhone() {
        return $this->phone;
    }

    function getFax() {
        return $this->fax;
    }

    function getMobile() {
        return $this->mobile;
    }

    function getPrivatePhone() {
        return $this->privatePhone;
    }

    function getDepartment() {
        return $this->department;
    }

    function getInsertBy() {
        return $this->insertBy;
    }
    
    // Getters FK
    
    function getProjectsCP() {
        return $this->projectsCP;
    }

    function getProjetRelContacts() {
        return $this->projetRelContacts;
    }

    function getGeneralPlans() {
        return $this->generalPlans;
    }

    // Setters
    
    function setId($id) {
        $this->id = $id;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setOldPassword($oldPassword) {
        $this->oldPassword = $oldPassword;
    }
  
    function setName($name) {
        $this->name = $name;
    }

    function setFirstname($firstname) {
        $this->firstname = $firstname;
    }
    
    function setInitials($initials) {
        $this->initials = $initials;
    }

    function setNameFirstname($nameFirstname) {
        $this->nameFirstname = $nameFirstname;
    }

    function setCompany($company) {
        $this->company = $company;
    }

    function setLevel($level) {
        $this->level = $level;
    }

    function setCp($cp) {
        $this->cp = $cp;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPhone($phone) {
        $this->phone = $phone;
    }

    function setFax($fax) {
        $this->fax = $fax;
    }

    function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    function setPrivatePhone($privatePhone) {
        $this->privatePhone = $privatePhone;
    }

    function setDepartment($department) {
        $this->department = $department;
    }

    function setInsertBy($insertBy) {
        $this->insertBy = $insertBy;
    }
    
    // Setters FK
    
    function setProjectsCP(Project $projectsCP) {
        $this->projectsCP = $projectsCP;
    }

    function setProjetRelContacts(ProjetRelContact $projetRelContacts) {
        $this->projetRelContacts = $projetRelContacts;
    }
    
    function setGeneralPlans(GeneralPlan $generalPlans) {
        $this->generalPlans = $generalPlans;
    }
    
    // Util
    
    function initiales($nom){
        $words = explode(" ", (str_replace('-', ' ', $nom)));
        $initiale = '';
 
	foreach($words as $init){
                $initiale .= $init{0};
    }
    return strtoupper($initiale);

    }


}
    
    