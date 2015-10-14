<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ProjectRepository
 *
 * @author SGOURMELON
 */
class ProjectRepository extends EntityRepository {
    
    public function getProjectsByUser($user){
        
        $qb = $this->createQueryBuilder('p');
        
         $qb->join('p.projetRelContacts', 'prc')
            //->select('prc')
            ->where('prc.contact = :user')
            ->setParameter('user', $user)
            ->orderBy('p.planStart', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    // Récupérer TOUS les Projets du chef de projets
    public function getProjectsByCP($cp){
        
        $qb = $this->createQueryBuilder('p');
        
        $qb->where('p.cp = :cp')
           ->setParameter('cp', $cp);
           
                 
        return $qb->getQuery()->getResult();
    }
    
    public function searchProjectsByCompanyName($companyName){
        
        $qb = $this->createQueryBuilder('p');
        
         $qb->join('p.company', 'cpny')
            ->where('cpny.name LIKE :companyName')
            ->setParameter('companyName', $companyName . '%');
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getAll(){
        
        $qb = $this->createQueryBuilder('p');
        
         $qb->orderBy('p.planStart', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
}
