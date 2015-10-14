<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 

/**
 * Description of Project
 * 
 * Référence les projets
 *
 * @author SGOURMELON
 */

/**
 * GlobalPlan
 *
 * @ORM\Table(name="PROJECTS")
 * @ORM\Entity(repositoryClass="Asmolding\Bundle\Entity\ProjectRepository")
 */
class Project {
   
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /*
     * Clé étrangère vers Company.id (cid dans DB)
     */
    /**
     *
     * @var Company
     * 
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="projects")
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="num", type="string", length=6)
     * //@Assert\NotBlank(message="Le numéro de projet doit être renseigné")
     * //@Assert\Regex(pattern="/^[0-9]{2}[_][0-9]{3}$/", message="Le numéro de projet doit être au format 01_234")
     * 
     */
    private $num;
    
    /**
     *
     * @var type
     * @Assert\NotBlank(message="Le numéro de projet doit être renseigné")
     * @Assert\Regex(pattern="/^[0-9]{2}$/", message="Le numéro doit contenir 2 chiffres") 
     */
    private $num1;
    
    /**
     *
     * @var type
     * @Assert\NotBlank(message="Le numéro de projet doit être renseigné")
     * @Assert\Regex(pattern="/^[0-9]{3}$/", message="Le numéro doit contenir 3 chiffres") 
     */
    private $num2;
    
    /**
     *
     * @var Contact
     * 
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="projectsCP")
     */
    private $cp;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=35)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="pathcode", type="string", length=16, nullable=true)
     */
    private $pathCode;

    /**
     * @var string
     *
     * @ORM\Column(name="descript", type="string", length=512, nullable=true)
     */
    private $description;

    /**
     * @var datetime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var date
     *
     * @ORM\Column(name="planstart", type="date")
     */
    private $planStart;
    
   /**
     *
     * @var boolean
     * 
     * //@ORM\Column(name="isArchived", type="boolean")
     */
    //private $isArchived = false;
    
    // Clés étrancgères (FK)
    
    
    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $image;
    
    /**
     *
     * @var GeneralPlan
     * 
     * @ORM\OneToMany(targetEntity="GeneralPlan", mappedBy="project")
     */
    private $generalPlans;
    
    /**
     *
     * @var GlobalPlan
     * 
     * @ORM\OneToMany(targetEntity="GlobalPlan", mappedBy="project")
     */
    private $globalPlans;
    
    /**
     *
     * @var Mold
     * 
     * @ORM\OneToMany(targetEntity="Mold", mappedBy="project", cascade={"remove"})
     */
    private $molds;
    
    /**
     *
     * @var MPR
     * 
     * @ORM\OneToMany(targetEntity="MPR", mappedBy="project")
     */
    private $mprs;
    
    /**
     *
     * @var ProjetRelContact
     * 
     * @ORM\OneToMany(targetEntity="ProjetRelContact", mappedBy="project")
     */
    private $projetRelContacts;
    
    /**
     *
     * @var TransferHistory
     * 
     * @ORM\OneToMany(targetEntity="TransferHistory", mappedBy="project")
     */
    private $transferHistories;

    // Getters

    function getId() {
        return $this->id;
    }

    function getCompany() {
        return $this->company;
    }

    function getNum() {
        return $this->num;
    }
    
    function getNum1() {
        return $this->num1;
    }

    function getNum2() {
        return $this->num2;
    }

    
    function getName() {
        return $this->name;
    }

    function getCp() {
        return $this->cp;
    }

    function getPathCode() {
        return $this->pathCode;
    }

    function getDescription() {
        return $this->description;
    }

    function getDate() {
        return $this->date;
    }

    function getPlanStart() {
        return $this->planStart;
    }
    
    /*
     * Archivage du projet
     */
    /*
    function getIsArchived() {
        return $this->isArchived;
    }
     */
    
    // Getters FK
    
    function getGeneralPlans() {
        return $this->generalPlans;
    }
    
    function getGlobalPlans() {
        return $this->globalPlans;
    }

    function getMolds() {
        return $this->molds;
    }

    function getMprs() {
        return $this->mprs;
    }

    function getProjetRelContacts() {
        return $this->projetRelContacts;
    }
    
    function getTransferHistories() {
        return $this->transferHistories;
    }
    
    function getImage() {
        return $this->image;
    }
        
    // Setters

    function setId($id) {
        $this->id = $id;
    }
    
    function setCompany($company) {
        $this->company = $company;
    }
    
    function setNum($num) {
        $this->num = $num;
    }
    
    function setNum1($num1) {
        $this->num1 = $num1;
    }

    function setNum2($num2) {
        $this->num2 = $num2;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setCp(Contact $cp) {
        $this->cp = $cp;
    }

    function setPathCode($pathCode) {
        $this->pathCode = $pathCode;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setDate($date) {
        $this->date = $date;
    }

    function setPlanStart($planStart) {
        $this->planStart = $planStart;
    }
    /*
     * Archivage du projet
     */
    /*
    function setIsArchived($isArchived) {
        $this->isArchived = $isArchived;
    }
    */

    // Setter FK
    
    function setGeneralPlans(GeneralPlan $generalPlans) {
        $this->generalPlans = $generalPlans;
    }
    
    function setGlobalPlans($globalPlans) {
        $this->globalPlans = $globalPlans;
    }

    function setMolds($molds) {
        $this->molds = $molds;
    }

    function setMprs($mprs) {
        $this->mprs = $mprs;
    }

    function setProjetRelContacts($projetRelContacts) {
        $this->projetRelContacts = $projetRelContacts;
    }
    
    function setTransferHistories(TransferHistory $transferHistories) {
        $this->transferHistories = $transferHistories;
    }
    
    function setImage(Image $image = null) {
        $this->image = $image;
        return $this;
    }
}
