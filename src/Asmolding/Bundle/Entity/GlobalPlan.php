<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of GlobalPlan
 * 
 * Référence le planning des projets (tâches)
 *
 * @author SGOURMELON
 */

/**
 * GlobalPlan
 *
 * @ORM\Table(name="GLOBALPLAN")
 * @ORM\Entity
 */
class GlobalPlan {
    
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /*
     * Clé étrangère vers Project.id
     */
    /**
     *
     * @var Project
     * 
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="globalPlans")
     */
    private $project;
    
    /*
     * Clé étrangère vers Mold.id
     */
    
   /**
    *
    * @var Mold
    * 
    * @ORM\ManyToOne(targetEntity="Mold", inversedBy="globalPlans")
    */
    private $mold;
    
    /**
     * @var string
     *
     * @ORM\Column(name="milestone", type="string", length=32)
     */
    private $milestone;
    
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
     * @ORM\Column(name="comment", type="boolean")
     */
    private $comment;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comment_txt", type="string", length=512)
     */
    private $commentText;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;
    
    // Getters et Setters
    
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

    function getColor() {
        return $this->color;
    }

    function getProgress() {
        return $this->progress;
    }
    
    function getSequence() {
        return $this->sequence;
    }

    function getComment() {
        return $this->comment;
    }

    function getCommentText() {
        return $this->commentText;
    }

    function getVisible() {
        return $this->visible;
    }

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

    function setColor($color) {
        $this->color = $color;
    }

    function setProgress($progress) {
        $this->progress = $progress;
    }

    function setSequence($sequence) {
        $this->sequence = $sequence;
    }

    function setComment($comment) {
        $this->comment = $comment;
    }

    function setCommentText($commentText) {
        $this->commentText = $commentText;
    }

    function setVisible($visible) {
        $this->visible = $visible;
    }


}
