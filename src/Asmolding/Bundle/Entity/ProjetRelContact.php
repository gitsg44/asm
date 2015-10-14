<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ProjetRelContact
 * 
 * Référence les liens entre Project et Contact (+ nom du contact référent)
 *
 * @author SGOURMELON
 */

/**
 * ProjetRelContact
 *
 * @ORM\Table(name="PROJ_REL_CONTACT")
 * @ORM\Entity(repositoryClass="Asmolding\Bundle\Entity\ProjetRelContactRepository")
 */
class ProjetRelContact {
    
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer") 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /*
     * Clé étrangère vers Project.id (cid dans DB)
     */
    /**
     *
     * @var Project
     * 
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projetRelContacts")
     */
    private $project;
    
    /*
     * Clé étrangère vers Contact.id (cid dans DB)
     */
    /**
     *
     * @var Contact
     * 
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="projetRelContacts")
     */
    private $contact;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;
    
    // Getters et Setters
    
    function getId() {
        return $this->id;
    }

    function getProject() {
        return $this->project;
    }

    function getContact() {
        return $this->contact;
    }

    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }
    function setProject($project) {
        $this->project = $project;
    }

    function setContact($contact) {
        $this->contact = $contact;
    }

    function setName($name) {
        $this->name = $name;
    }

}
