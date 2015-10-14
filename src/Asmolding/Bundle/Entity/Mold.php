<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Description of Mold
 * 
 * Référence les affaires
 *
 * @author SGOURMELON
 */

/**
 * Molds
 *
 * @ORM\Table(name="MOLDS")
 * @ORM\Entity(repositoryClass="Asmolding\Bundle\Entity\MoldRepository")
 */
class Mold {
    
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
    * @ORM\ManyToOne(targetEntity="Company", inversedBy="molds")
    */
    private $company;
    
    /*
     * Clé étrangère vers Project.id (pid dans DB)
     */
     /**
    *
    * @var Project
    * 
    * @ORM\ManyToOne(targetEntity="Project", inversedBy="molds")
    */
    private $project;
    
    
    private $numProject;
    
    /**
    * @var string
    *
    * @ORM\Column(name="num", type="string", length=11)
    * 
    */
    private $num;
    
    /**
     *
     * @var type 
     * @Assert\NotBlank(message="Le premier numéro d'affaire doit être renseigné")
     * @Assert\Regex(pattern="/^[0-9]{3}$/", message="Le premier numéro doit contenir 3 chiffres")
     */
    private $num1;
    
    /**
     *
     * @var type 
     * @Assert\NotBlank(message="Le DAS doit être renseigné")
     */
    private $das;
    
    /**
     *
     * @var type 
     * @Assert\NotBlank(message="Le second numéro d'affaire doit être renseigné")
     * @Assert\Regex(pattern="/^[0-9]{3}$/", message="Le second numéro doit contenir 3 chiffres")
     */
    private $num2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pathcode", type="string", length=18, nullable=true)
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
     *
     * @var type 
     * 
     * @ORM\Column(name="solution", type="string", length=30)
     */
    private $solution;
    
    /**
     *
     * @var boolean
     * 
     * @ORM\Column(name="isArchived", type="boolean")
     */
    private $isArchived = false;
    
    // Clés étrangères (FK)
    
     /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $image;
    
    /**
     *
     * @var GeneralPlan
     * 
     * @ORM\OneToMany(targetEntity="GeneralPlan", mappedBy="mold")
     */
    private $generalPlans;
    
    /**
     *
     * @var GlobalPlan
     * 
     * @ORM\OneToMany(targetEntity="GlobalPlan", mappedBy="mold")
     */
    private $globalPlans;
    
    /**
     *
     * @var MPR
     * 
     * @ORM\OneToMany(targetEntity="MPR", mappedBy="mold")
     */
    private $mprs;
    
    /**
     *
     * @var MPRDetail
     * 
     * @ORM\OneToMany(targetEntity="MPRDetail", mappedBy="mold")
     */
    private $mprDetails;
       
     /**
      * @ORM\ManyToMany(targetEntity="Asmolding\Bundle\Entity\Milestone", cascade={"persist"})
      */
    private $milestones;
    
    // Constructeur
    public function __construct()
    {
    $this->milestones = new ArrayCollection();
    }

    // Getters
    
    function getId() {
        return $this->id;
    }

    function getCompany() {
        return $this->company;
    }

    function getProject() {
        return $this->project;
    }
    
    function getNumProject() {
        return $this->numProject;
    }

    function getNum() {
        return $this->num;
    }
    
    function getNum1() {
        return $this->num1;
    }

    function getDas() {
        return $this->das;
    }

    function getNum2() {
        return $this->num2;
    }

    
    function getName() {
        return $this->name;
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

    function getSolution() {
        return $this->solution;
    }
    
    function getIsArchived() {
        return $this->isArchived;
    }

    
    // Getters FK
    
    function getImage() {
        return $this->image;
    }
    
    function getGeneralPlans() {
        return $this->generalPlans;
    }

    function getGlobalPlans() {
        return $this->globalPlans;
    }

    function getMprs() {
        return $this->mprs;
    }

    function getMprDetails() {
        return $this->mprDetails;
    }
    
    function getMilestones() {
        return $this->milestones;
    }
    
    // Setters
    
    function setId($id) {
        $this->id = $id;
    }

    function setCompany($company) {
        $this->company = $company;
    }
    
    function setProject($project) {
        $this->project = $project;
    }
    
    function setNumProject($numProject) {
        $this->numProject = $numProject;
    }

    function setNum($num) {
        $this->num = $num;
    }
    
    function setNum1($num1) {
        $this->num1 = $num1;
    }

    function setDas($das) {
        $this->das = $das;
    }

    function setNum2($num2) {
        $this->num2 = $num2;
    }

    function setName($name) {
        $this->name = $name;
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
    
    function setSolution($solution) {
        $this->solution = $solution;
    }
    
    function setIsArchived($isArchived) {
        $this->isArchived = $isArchived;
    }

        
    // Setters FK
    
    function setGeneralPlans(GeneralPlan $generalPlans) {
        $this->generalPlans = $generalPlans;
    }

    function setGlobalPlans(GlobalPlan $globalPlans) {
        $this->globalPlans = $globalPlans;
    }

    function setMprs(MPR $mprs) {
        $this->mprs = $mprs;
    }

    function setMprDetails(MPRDetail $mprDetails) {
        $this->mprDetails = $mprDetails;
    }
    
    function setImage(Image $image = null) {
        $this->image = $image;
        return $this;
    }
    
    public function addMilestone(Milestone $milestone)
    {
    // Ici, on utilise l'ArrayCollection vraiment comme un tableau
    $this->milestones[] = $milestone;

    return $this;
    }

    public function removeMilestone(Milestone $milestone)
    {
      // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
      $this->milestones->removeElement($milestone);
    }



}
