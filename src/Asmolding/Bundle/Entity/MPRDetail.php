<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of MPRDetail
 * 
 * Référence le détail du planning des affaires (PJ, photos, rapports, videos,...)
 *
 * @author SGOURMELON
 */

/**
 * GlobalPlan
 *
 * @ORM\Table(name="MPRDETAIL")
 * @ORM\Entity
 */
class MPRDetail {
    
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /*
     * Clé étrangère vers Mold.id (mid dans DB)
     */
    /**
     *
     * @var Mold
     * 
     * @ORM\ManyToOne(targetEntity="Mold", inversedBy="mprDetails")
     */
    private $mold;
    
    /*
     * Clé étrangère vers MPR.id (mprid dans DB)
     */
    /**
     *
     * @var MPR
     * 
     * @ORM\ManyToOne(targetEntity="MPR", inversedBy="mprDetails")
     */
    private $mpr;
    
    /**
     * @var string
     *
     * @ORM\Column(name="plantype", type="string", length=35)
     */
    private $piecesJointes;
    
    /**
     * @var string
     *
     * @ORM\Column(name="detail1", type="string", length=1024)
     */
    private $detail1;
    
    /**
     * @var string
     *
     * @ORM\Column(name="detail2", type="string", length=1024)
     */
    private $detail2;
    
    // Gatters & Setters
    
    function getId() {
        return $this->id;
    }

    function getMold() {
        return $this->mold;
    }

    function getMpr() {
        return $this->mpr;
    }

    function getPiecesJointes() {
        return $this->piecesJointes;
    }

    function getDetail1() {
        return $this->detail1;
    }

    function getDetail2() {
        return $this->detail2;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setMold($mold) {
        $this->mold = $mold;
    }

    function setMpr($mpr) {
        $this->mpr = $mpr;
    }

    function setPiecesJointes($piecesJointes) {
        $this->piecesJointes = $piecesJointes;
    }

    function setDetail1($detail1) {
        $this->detail1 = $detail1;
    }

    function setDetail2($detail2) {
        $this->detail2 = $detail2;
    }
   
}
