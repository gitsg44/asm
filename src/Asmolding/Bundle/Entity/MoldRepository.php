<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of MoldRepository
 *
 * @author SGOURMELON
 */
class MoldRepository extends EntityRepository{
    
    // Récupérer les moules du client
    public function getMoldsByCompany($company){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->where('m.company = :company')
            ->setParameter('company', $company)
            ->orderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules du chef de projets
    public function getMoldsByCP($cp){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->where('p.cp = :cp')
           ->setParameter('cp', $cp)
           ->orderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer les moules NON ARCHIVES du chef de projets
    public function getNotArchivedMoldsByCP($cp){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->where('p.cp = :cp')
           ->andWhere('m.archived = :bool')
           ->setParameter('cp', $cp)
           ->setParameter(':bool', 0)
           ->orderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules
    public function getAll(){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->join('m.company', 'c')
            ->join('m.project', 'p')
            ->join('p.cp', 'cp')
            ->addSelect('c')
            ->addSelect('p')
            ->addSelect('cp')
            ->addorderBy('c.name', 'ASC')
            ->addOrderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules non archivés
    public function getAllNotArchived(){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->join('m.company', 'c')
            ->join('m.project', 'p')
            ->join('p.cp', 'cp')
            ->addSelect('c')
            ->addSelect('p')
            ->addSelect('cp')
            ->where('m.archived = :bool')
            ->setParameter(':bool', 0)
            ->addorderBy('c.name', 'ASC')
            ->addOrderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARCHIVES par projet
    public function getNotArchivedByProject($project) {
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->addSelect('p')
           ->where('p = :project')
           ->andWhere('m.archived = :bool')
           ->setParameter(':project', $project)
           ->setParameter(':bool', 0);
        
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules ARCHIVES par projet
    public function getArchivedByProject($project) {
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->addSelect('p')
           ->where('p = :project')
           ->andWhere('m.archived = :bool')
           ->setParameter(':project', $project)
           ->setParameter(':bool', 1);
        
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARHIVES par chef de projets et par projet
    public function getNotArchivedByCPAndByProject($cp, $project){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->where('p.cp = :cp')
           ->andWhere('p = :project')
           ->andWhere('m.archived = :bool')
           ->setParameter('cp', $cp)
           ->setParameter(':bool', 0)
           ->setParameter(':project', $project)
           ->orderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARCHIVES par utilisateur et par projet
    public function getNotArchivedByUserAndByProject($user, $project){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->join('m.company', 'c')
            ->join('m.project', 'p')
            ->join('p.cp', 'cp')
            ->join('p.projetRelContacts', 'prc')
            ->addSelect('c')
            ->addSelect('p')
            ->addSelect('cp')
            ->addSelect('prc')
            ->where('m.archived = :bool')
            ->andWhere('prc.contact = :user')
            ->andWhere('p = :project')
            ->setParameter(':bool', 0)
            ->setParameter(':user', $user)
            ->setParameter(':project', $project)
            ->addorderBy('c.name', 'ASC')
            ->addOrderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARHIVES par chef de projets et par client
    public function getNotArchivedByCPAndByClient($cp, $company){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->join('m.company', 'c')
           ->where('p.cp = :cp')
           ->andWhere('c = :company')
           ->andWhere('m.archived = :bool')
           ->setParameter('cp', $cp)
           ->setParameter(':bool', 0)
           ->setParameter(':company', $company)
           ->orderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARCHIVES par client
    public function getNotArchivedByClient($company){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->join('m.company', 'c')
            ->join('m.project', 'p')
            ->join('p.cp', 'cp')
            ->addSelect('c')
            ->addSelect('p')
            ->addSelect('cp')
            ->where('m.archived = :bool')
            ->andWhere('c = :company')
            ->setParameter(':bool', 0)
            ->setParameter(':company', $company)
            ->addorderBy('c.name', 'ASC')
            ->addOrderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
   
    // Récupérer TOUS les moules NON ARHIVES par chef de projets et par DAS
    public function getNotArchivedByCPAndByDas($cp, $das){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->where('p.cp = :cp')
           ->andWhere('m.num LIKE :num')
           ->andWhere('m.archived = :bool')
           ->setParameter('cp', $cp)
           ->setParameter(':bool', 0)
           ->setParameter(':num', '%' . $das . '%')
           ->orderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARCHIVES par DAS
    public function getNotArchivedByDas($das){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->join('m.project', 'p')
            ->join('p.cp', 'cp')
            ->addSelect('p')
            ->addSelect('cp')
            ->where('m.archived = :bool')
            ->andWhere('m.num LIKE :num')
            ->setParameter(':bool', 0)
            ->setParameter(':num', '%' . $das . '%')
            ->addOrderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARHIVES par chef de projets et par Solution
    public function getNotArchivedByCPAndBySolution($cp, $solution){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->where('p.cp = :cp')
           ->andWhere('m.solution = :solution')
           ->andWhere('m.archived = :bool')
           ->setParameter('cp', $cp)
           ->setParameter(':bool', 0)
           ->setParameter(':solution', $solution)
           ->orderBy('m.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les moules NON ARCHIVES par Solution
    public function getNotArchivedBySolution($solution){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->join('m.project', 'p')
           ->join('p.cp', 'cp')
           ->addSelect('p')
           ->addSelect('cp')
           ->where('m.archived = :bool')
           ->andWhere('m.solution = :solution')
           ->setParameter(':bool', 0)
           ->setParameter(':solution', $solution)
           ->addOrderBy('m.name', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer la solution par moule
    public function getSolutionByMold($mold){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->where('m = :mold')
           ->setParameter(':mold', $mold);
                
       return $qb;
    }
}
