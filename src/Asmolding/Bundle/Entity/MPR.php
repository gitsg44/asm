<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of MPR
 * 
 * Référence le planning des affaires
 *
 * @author SGOURMELON
 */

/**
 *
 * @ORM\Table(name="MPR")
 * @ORM\Entity
 */
class MPR {
    
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /*
     * Clé étrangère vers Project.id (pid dans DB)
     */
    /**
     *
     * @var Project
     * 
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="mprs")
     */
    private $project;
    
    /*
     * Clé étrangère vers Mold.id (mid dans DB)
     */
     /**
     *
     * @var Mold
     * 
     * @ORM\ManyToOne(targetEntity="Mold", inversedBy="mprs")
     */
    private $mold;
    
    /**
     * @var string
     *
     * @ORM\Column(name="plantype", type="string", length=32)
     */
    private $taches;
    
    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=8)
     */
    private $color;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="progress", type="integer")
     */
    private $progress;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="sequence", type="string", length=3)
     */
    private $sequence;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="hidden", type="boolean")
     */
    private $hidden;
    
    // Clés Etrangères (FK)
    
    /**
     *
     * @var MPRDetail
     * 
     * @ORM\OneToMany(targetEntity="MPRDetail", mappedBy="mpr")
     */
    private $mprDetails;
    
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

    function getTaches() {
        return $this->taches;
    }

    function getColor() {
        return $this->color;
    }

    function getProgress() {
        return $this->progress;
    }

    function getSequence() {
        return $this->sequence;
    }

    function getHidden() {
        return $this->hidden;
    }
    
    // Getters FK
    
    function getMprDetails() {
        return $this->mprDetails;
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
    
    function setTaches($taches) {
        $this->taches = $taches;
    }

    function setColor($color) {
        $this->color = $color;
    }

    function setProgress($progress) {
        $this->progress = $progress;
    }

    function setSequence($sequence) {
        $this->sequence = $sequence;
    }

    function setHidden($hidden) {
        $this->hidden = $hidden;
    }
    
    // Setters FK
    
    function setMprDetails(MPRDetail $mprDetails) {
        $this->mprDetails = $mprDetails;
    }  
}
