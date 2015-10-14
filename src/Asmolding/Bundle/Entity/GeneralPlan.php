<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Asmolding\Bundle\Entity\Milestone;
use Asmolding\Bundle\Entity\Mold;
use Asmolding\Bundle\Entity\Project;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of GeneralPlan
 *
 * @author SGOURMELON
 */
/**
 *
 * @ORM\Table(name="GeneralPlan", uniqueConstraints={@UniqueConstraint(name="uniq", columns={"project_id", "mold_id", "milestone_id"})})
 * @ORM\Entity(repositoryClass="GeneralPlanRepository")
 */
class GeneralPlan {
    
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     *
     * @var Project
     * 
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="generalPlans")
     */
    private $project;
    
    /*
     * Clé étrangère vers Mold.id
     */
    
   /**
    *
    * @var Mold
    * 
    * @ORM\ManyToOne(targetEntity="Mold", inversedBy="generalPlans")
    */
    private $mold;
    
    /**
    *
    * @var Milestone
    * 
    * @ORM\ManyToOne(targetEntity="Milestone", inversedBy="generalPlans")
    */
    private $milestone;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="previsionnel", type="date", nullable=true)
     */
    private $previsionnel;
    
     /**
     *
     * @var string
     * 
     * @ORM\Column(name="reel", type="date", nullable=true)
     */
    private $reel;
    
    /**
     *
     * @var type 
     * //@Assert\NotBlank(message="Le numéro de semaine prévisionnelle doit être renseigné", groups={"previsionnel", "all"})
     * @Assert\Range(
     *  min=1, 
     *  max=53, 
     *  minMessage="Le numéro de semaine doit être supérieur à 1", 
     *  maxMessage="Le numéro de semaine doit être inférieur à 53",
     *  invalidMessage="Valeur invalide : Entrez un nombre.",
     *  groups={"previsionnel", "all"})
     */
    private $pweek;
    
    /**
     *
     * @var type 
     * //@Assert\NotBlank(message="Le numéro de jour prévisionnel doit être renseigné", groups={"previsionnel", "all"})
     * @Assert\Range(
     *  min=1, 
     *  max=6, 
     *  minMessage="Le numéro de jour doit être supérieur à 1", 
     *  maxMessage="Le numéro de jour doit être inférieur à 6",
     *  invalidMessage="Valeur invalide : Entrez un nombre.",
     *  groups={"previsionnel", "all"})
     */
    private $pday;
    
    /**
     *
     * @var type 
     * //@Assert\NotBlank(message="Le numéro de jour prévisionnel doit être renseigné", groups={"previsionnel", "all"})
     * //@Assert\Regex(pattern="/^[0-9]{4}$/", message="Le numéro doit contenir 4 chiffres")
     *  //groups={"previsionnel", "all"})
     */
    private $pyear;
    
    /**
     *
     * @var type 
     * //@Assert\NotBlank(message="Le numéro de semaine réelle doit être renseigné", groups={"reel", "all"})
     * @Assert\Range(
     *  min=1, 
     *  max=53, 
     *  minMessage="Le numéro de semaine doit être supérieur à 1", 
     *  maxMessage="Le numéro de semaine doit être inférieur à 53",
     *  invalidMessage="Valeur invalide : Entrez un nombre.",
     *  groups={"reel", "all"})
     */
    private $rweek;
    
    /**
     *
     * @var type 
     * //@Assert\NotBlank(message="Le numéro de jour réel doit être renseigné", groups={"reel", "all"})
     * @Assert\Range(
     *  min=1, 
     *  max=6, 
     *  minMessage="Le numéro de jour doit être supérieur à 1", 
     *  maxMessage="Le numéro de jour doit être inférieur à 6",
     *  invalidMessage="Valeur invalide : Entrez un nombre.",
     *  groups={"reel", "all"})
     */
    private $rday;
    
    private $ryear;
    
    private $test;
    
    /**
     *
     * @var datetime
     * 
     * @ORM\Column(name="updateTime", type="datetime")
     */
    private $updateTime;
    
    /**
     *
     * @var Contact
     * 
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="generalPlans")
     */
    private $updateUser;


    // Constructeur 
    
    public function __construct()
    {
        $this->previsionnel = '1.1';
        $this->reel = '1.1';
    }

    
    // Getters
    
    function getId() {
        return $this->id;
    }

    function getProject() {
        return $this->project;
    }

    function getMold() {
        return $this->mold;
    }

    function getMilestone() {
        return $this->milestone;
    }

    function getPrevisionnel() {
        return $this->previsionnel;
    }

    function getReel() {
        return $this->reel;
    }

    function getPweek() {
        return $this->pweek;
    }

    function getPday() {
        return $this->pday;
    }
    
    function getPyear() {
        return $this->pyear;
    }
    
    function getRweek() {
        return $this->rweek;
    }

    function getRday() {
        return $this->rday;
    }
    
    function getRyear() {
        return $this->ryear;
    }
     
    function getTest() {
        return $this->test;
    }
    
    function getUpdateTime() {
        return $this->updateTime;
    }

    function getUpdateUser() {
        return $this->updateUser;
    }

        
    // Setters
    
    function setId($id) {
        $this->id = $id;
    }

    function setProject($project) {
        $this->project = $project;
    }

    function setMold($mold) {
        $this->mold = $mold;
    }

    function setMilestone($milestone) {
        $this->milestone = $milestone;
    }

    function setPrevisionnel($previsionnel) {
        $this->previsionnel = $previsionnel;
    }

    function setReel($reel) {
        $this->reel = $reel;
    }

    function setPweek($pweek) {
        $this->pweek = $pweek;
    }

    function setPday($pday) {
        $this->pday = $pday;
    }
    
    function setPyear($pyear) {
        $this->pyear = $pyear;
    }

    function setRweek($rweek) {
        $this->rweek = $rweek;
    }

    function setRday($rday) {
        $this->rday = $rday;
    }

    function setRyear($ryear) {
        $this->ryear = $ryear;
    }

    function setTest($test) {
        $this->test = $test;
    }

    function setUpdateTime($updateTime) {
        $this->updateTime = $updateTime;
    }

    function setUpdateUser($updateUser) {
        $this->updateUser = $updateUser;
    }    
}
