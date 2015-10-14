<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Symfony\Component\Validator\Constraints as Assert; 

/**
 * Description of Image
 *
 * @author SGOURMELON
 */

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Entity\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="extension", type="string", length=255)
   */
  private $extension;

  /**
   * @ORM\Column(name="alt", type="string", length=255)
   */
  private $alt;
  
 /**
  *
  * @var type 
  * //@Assert\Image(
  * //     maxWidth = 70,
  * //     maxWidthMessage = "La largeur de l'image est trop grande ({{ width }}px). La largeur maximale autorisée est de {{ max_width }}px",
  * //     maxHeight = 30,
  * //     maxHeightMessage = "La hauteur de l'image est trop grande ({{ height }}px). La hauteur maximale autorisée est de {{ max_height }}px"
  *  )
  */
  private $file;
  
   // On ajoute cet attribut pour y stocker le nom du fichier temporairement
  private $tempFilename;

  // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
  public function setFile(UploadedFile $file)
  {
    $this->file = $file;

    // On vérifie si on avait déjà un fichier pour cette entité
    if (null !== $this->extension) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->extension;

      // On réinitialise les valeurs des attributs url et alt
      $this->extension = null;
      $this->alt = null;
    }
  }

  /**
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   */
  public function preUpload()
  {
    // Si jamais il n'y a pas de fichier (champ facultatif)
    if (null === $this->file) {
      return;
    }

    // Le nom du fichier est son id, on doit juste stocker également son extension
    // Pour faire propre, on devrait renommer cet attribut en « extension », plutôt que « url »
    $this->extension = $this->file->guessExtension();

    // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
    $this->alt = $this->file->getClientOriginalName();
  }

  /**
   * @ORM\PostPersist()
   * @ORM\PostUpdate()
   */
  public function upload()
  {
    // Si jamais il n'y a pas de fichier (champ facultatif)
    if (null === $this->file) {
      return;
    }

    // Si on avait un ancien fichier, on le supprime
    if (null !== $this->tempFilename) {
      $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
      if (file_exists($oldFile)) {
        unlink($oldFile);
      }
    }

    // On déplace le fichier envoyé dans le répertoire de notre choix
    $this->file->move(
      $this->getUploadRootDir(), // Le répertoire de destination
      $this->id.'.'.$this->extension   // Le nom du fichier à créer, ici « id.extension »
    );
  }

  /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload()
  {
    // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
    if (file_exists($this->tempFilename)) {
      // On supprime le fichier
      unlink($this->tempFilename);
    }
  }

  public function getUploadDir()
  {
    // On retourne le chemin relatif vers l'image pour un navigateur
    return 'uploads/img';
  }

  protected function getUploadRootDir()
  {
    // On retourne le chemin relatif vers l'image pour notre code PHP
    return __DIR__.'/../../../../web/'.$this->getUploadDir();
  }

  // On récupère le chemin pour afficher l'image
  public function getWebPath()
  {
    return $this->getUploadDir().'/'.$this->getId().'.'.$this->getExtension();
  }

  // Getters et setters
  function getId() {
      return $this->id;
  }

  function getExtension() {
      return $this->extension;
  }

  function getAlt() {
      return $this->alt;
  }

  public function getFile()
  {
    return $this->file;
  }
  
  function setId($id) {
      $this->id = $id;
  }

  function setExtension($extension) {
      $this->extension = $extension;
      return $this;
  }

  function setAlt($alt) {
      $this->alt = $alt;
  }

}