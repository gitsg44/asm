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
 * Description of Milestone
 * 
 * Référenceles jalons
 *
 * @author SGOURMELON
 */
/**
 *
 * @ORM\Table(name="Milestones")
 * @ORM\Entity(repositoryClass="Asmolding\Bundle\Entity\MilestoneRepository")
 */
class Milestone {
    
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
     * @var string
     * 
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank(message="Le nom du jalon doit être renseigné")
     */
    private $name;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="sequence", type="integer")
     * @Assert\NotBlank(message="L'ordre du jalon doit être renseigné")
     */
    private $sequence;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="das", type="string", length=5, nullable=true)
     * @Assert\NotBlank(message="Le DAS du jalon doit être renseigné")
     */
    private $das;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="solution", type="string", length=50, nullable=true)
     * //@Assert\NotBlank(message="La solution du jalon doit être renseigné")
     */
    private $solution;
    
    /**
     *
     * @var GeneralPlan
     * 
     * @ORM\OneToMany(targetEntity="GeneralPlan", mappedBy="milestone")
     */
    private $generalPlans;
    
    // Getters
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getSequence() {
        return $this->sequence;
    }

    function getVisible() {
        return $this->visible;
    }
    
    function getDas() {
        return $this->das;
    }

    function getSolution() {
        return $this->solution;
    }
    
    function getGeneralPlans() {
        return $this->generalPlans;
    }

    // Setters
    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setSequence($sequence) {
        $this->sequence = $sequence;
    }

    function setVisible($visible) {
        $this->visible = $visible;
    }

    function setDas($das) {
        $this->das = $das;
    }

    function setSolution($solution) {
        $this->solution = $solution;
    }
    
    function setGeneralPlans(GeneralPlan $generalPlans) {
        $this->generalPlans = $generalPlans;
    }
   
}
