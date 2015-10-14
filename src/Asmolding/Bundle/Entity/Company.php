<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Company
 * 
 * Référence les clients (entreprises)
 *
 * @author SGOURMELON
 */

/**
 * @ORM\Table(name="COMPANIES")
 * @ORM\Entity(repositoryClass="Asmolding\Bundle\Entity\CompanyRepository")
 */
class Company {
    
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer" ) 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank(message="Le nom du client doit être renseigné", groups={"Default", "search"})
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pathcode", type="string", length=8, nullable=true)
     */
    private $pathcode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name2", type="string", length=50, nullable=true)
     */
    private $name2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="legalform", type="string", length=35, nullable=true)
     */
    private $legalform;
    
    /**
     * @var string
     *
     * @ORM\Column(name="legalform2", type="string", length=35, nullable=true)
     */
    private $legalform2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2)
     */
    private $country;
    
    /**
     * @var string
     *
     * @ORM\Column(name="country2", type="string", length=2, nullable=true)
     */
    private $country2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=50)
     * @Assert\NotBlank(message="L'adresse doit être renseignée")
     */
    private $address;
    
    /**
     * @var string
     *
     * @ORM\Column(name="address2", type="string", length=50, nullable=true)
     */
    private $address2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=8)
     * @Assert\NotBlank(message="Le code postal doit être renseigné")
     */
    private $zip;
    
    /**
     * @var string
     *
     * @ORM\Column(name="zip2", type="string", length=8, nullable=true)
     */
    private $zip2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=35)
     * @Assert\NotBlank(message="La ville doit être renseignée")
     */
    private $city;
    
    /**
     * @var string
     *
     * @ORM\Column(name="city2", type="string", length=35, nullable=true)
     */
    private $city2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=25, nullable=true)
     */
    private $phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone2", type="string", length=20, nullable=true)
     */
    private $phone2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=20, nullable=true)
     */
    private $fax;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fax2", type="string", length=20, nullable=true)
     */
    private $fax2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=35, nullable=true)
     * @Assert\Email(message="'{{ value }}' n'est pas une adresse mail valide")
     */
    private $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email2", type="string", length=35, nullable=true)
     * @Assert\Email(message="'{{ value }}' n'est pas une adresse mail valide")
     */
    private $email2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="www", type="string", length=50, nullable=true)
     */
    private $www;
    
    /**
     * @var string
     *
     * @ORM\Column(name="www2", type="string", length=50, nullable=true)
     */
    private $www2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="branche", type="string", length=256, nullable=true)
     */
    private $branche;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="level", type="boolean")
     */
    private $level;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="reglevel", type="boolean")
     */
    private $reglevel;
    
    /**
     * @var string
     *
     * @ORM\Column(name="regist", type="datetime")
     */
    private $regist;
    
    // Clés étrangères (FK)
    
    /**
     *
     * @var Contact
     * 
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="company", cascade={"remove"})
     */
    private $contacts;
    
    /**
     *
     * @var Mold
     * 
     * @ORM\OneToMany(targetEntity="Mold", mappedBy="company")
     */
    private $molds;
    /**
     *
     * @var Project
     * 
     * @ORM\OneToMany(targetEntity="Project", mappedBy="company", cascade={"remove"})
     */
    private $projects;
    /**
     *
     * @var TransferHistory
     * 
     * @ORM\OneToMany(targetEntity="TransferHistory", mappedBy="company")
     */
    private $transferHistories;
    
    // Getters
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getPathcode() {
        return $this->pathcode;
    }

    function getName2() {
        return $this->name2;
    }

    function getLegalform() {
        return $this->legalform;
    }

    function getLegalform2() {
        return $this->legalform2;
    }

    function getCountry() {
        return $this->country;
    }

    function getCountry2() {
        return $this->country2;
    }

    function getAddress() {
        return $this->address;
    }

    function getAddress2() {
        return $this->address2;
    }

    function getZip() {
        return $this->zip;
    }

    function getZip2() {
        return $this->zip2;
    }

    function getCity() {
        return $this->city;
    }

    function getCity2() {
        return $this->city2;
    }

    function getPhone() {
        return $this->phone;
    }

    function getPhone2() {
        return $this->phone2;
    }

    function getFax() {
        return $this->fax;
    }

    function getFax2() {
        return $this->fax2;
    }

    function getEmail() {
        return $this->email;
    }

    function getEmail2() {
        return $this->email2;
    }

    function getWww() {
        return $this->www;
    }

    function getWww2() {
        return $this->www2;
    }

    function getBranche() {
        return $this->branche;
    }

    function getLevel() {
        return $this->level;
    }

    function getReglevel() {
        return $this->reglevel;
    }

    function getRegist() {
        return $this->regist;
    }
    
    // Getters FK
    
    function getContacts() {
        return $this->contacts;
    }
    
    function getMolds() {
        return $this->molds;
    }

    function getProjects() {
        return $this->projects;
    }

    function getTransferHistories() {
        return $this->transferHistories;
    }
    
    // Setters

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPathcode($pathcode) {
        $this->pathcode = $pathcode;
    }

    function setName2($name2) {
        $this->name2 = $name2;
    }

    function setLegalform($legalform) {
        $this->legalform = $legalform;
    }

    function setLegalform2($legalform2) {
        $this->legalform2 = $legalform2;
    }

    function setCountry($country) {
        $this->country = $country;
    }

    function setCountry2($country2) {
        $this->country2 = $country2;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function setAddress2($address2) {
        $this->address2 = $address2;
    }

    function setZip($zip) {
        $this->zip = $zip;
    }

    function setZip2($zip2) {
        $this->zip2 = $zip2;
    }

    function setCity($city) {
        $this->city = $city;
    }

    function setCity2($city2) {
        $this->city2 = $city2;
    }

    function setPhone($phone) {
        $this->phone = $phone;
    }

    function setPhone2($phone2) {
        $this->phone2 = $phone2;
    }

    function setFax($fax) {
        $this->fax = $fax;
    }

    function setFax2($fax2) {
        $this->fax2 = $fax2;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setEmail2($email2) {
        $this->email2 = $email2;
    }

    function setWww($www) {
        $this->www = $www;
    }

    function setWww2($www2) {
        $this->www2 = $www2;
    }

    function setBranche($branche) {
        $this->branche = $branche;
    }

    function setLevel($level) {
        $this->level = $level;
    }

    function setReglevel($reglevel) {
        $this->reglevel = $reglevel;
    }

    function setRegist($regist) {
        $this->regist = $regist;
    }
    
    // Setters FK
    
    function setContacts(Contact $contacts) {
        $this->contacts = $contacts;
    }
    
    function setMolds(Mold $molds) {
        $this->molds = $molds;
    }

    function setProjects(Project $projects) {
        $this->projects = $projects;
    }

    function setTransferHistories(TransferHistory $transferHistories) {
        $this->transferHistories = $transferHistories;
    }
    
}
