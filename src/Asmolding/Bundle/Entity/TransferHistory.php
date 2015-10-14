<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of TransferHistory
 * 
 * Référence la gestion du FTP
 *
 * @author SGOURMELON
 */

/**
 * GlobalPlan
 *
 * @ORM\Table(name="TRANSFERHISTORY")
 * @ORM\Entity
 */
class TransferHistory {
    
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
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="transferHistories")
     */
    private $company;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sendername", type="string", length=50)
     */
    private $senderName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="projectname", type="string", length=32)
     */
    private $projectName;
    
    /*
     * Clé étrangère vers project.id (pid dans DB)
     */
    /**
     *
     * @var Project
     * 
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="transferHistories")
     */
    private $project;
    
    /**
     * @var string
     *
     * @ORM\Column(name="moldname", type="string", length=32)
     */
    private $moldName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="filetype", type="string", length=17)
     */
    private $fileType;
    
    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=64)
     */
    private $fileName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="string", length=256)
     */
    private $filePath;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="view", type="integer")
     */
    private $view;
    
    /**
     * @var string
     *
     * @ORM\Column(name="viewer", type="string", length=50)
     */
    private $viewer;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=256)
     */
    private $comment;
    
    // Getters et Setters
    
    function getId() {
        return $this->id;
    }
    
    function getCompany() {
        return $this->company;
    }
    
    function getSenderName() {
        return $this->senderName;
    }

    function getProjectName() {
        return $this->projectName;
    }

    function getProject() {
        return $this->project;
    }

    function getMoldName() {
        return $this->moldName;
    }

    function getFileType() {
        return $this->fileType;
    }

    function getFileName() {
        return $this->fileName;
    }

    function getFilePath() {
        return $this->filePath;
    }

    function getView() {
        return $this->view;
    }

    function getViewer() {
        return $this->viewer;
    }

    function getDate() {
        return $this->date;
    }

    function getComment() {
        return $this->comment;
    }

    function setId($id) {
        $this->id = $id;
    }
    
    function setCompany($company) {
        $this->company = $company;
    }
    
    function setSenderName($senderName) {
        $this->senderName = $senderName;
    }

    function setProjectName($projectName) {
        $this->projectName = $projectName;
    }

    function setProject($project) {
        $this->project = $project;
    }

    function setMoldName($moldName) {
        $this->moldName = $moldName;
    }

    function setFileType($fileType) {
        $this->fileType = $fileType;
    }

    function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    function setFilePath($filePath) {
        $this->filePath = $filePath;
    }

    function setView($view) {
        $this->view = $view;
    }

    function setViewer($viewer) {
        $this->viewer = $viewer;
    }

    function setDate($date) {
        $this->date = $date;
    }

    function setComment($comment) {
        $this->comment = $comment;
    }


}
