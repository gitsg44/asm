<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ContactRepository
 *
 * @author SGOURMELON
 */
class ContactRepository extends EntityRepository {
    
    public function searchContactsByName($name)
    {
        
        $qb = $this->createQueryBuilder('c');
        
         $qb->where('c.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->orderBy('c.name');
         
                 
        return $qb->getQuery()->getResult();
    }
    
    public function searchContactsByCompanyName($companyName) {
        
         $qb = $this->createQueryBuilder('c');
        
         $qb->join('c.company', 'cpny')
            ->where('cpny.name LIKE :companyName')
            ->setParameter('companyName', $companyName . '%');
                 
        return $qb->getQuery()->getResult();
        
    }
    
    public function getContactsByCompany($company)
    {   
        $qb = $this->createQueryBuilder('c');
        
         $qb->where('c.company = :company')
              ->setParameter('company', $company);
                 
        return $qb;
    }
    
    public function getContactsWithCompany(){
        
        $qb = $this->createQueryBuilder('c');
        
         $qb->join('c.company', 'a')
            ->setMaxResults(10)
            ->orderBy('c.company');
         
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getCps(){
        
        $qb = $this->createQueryBuilder('c');
        
        $qb->where('c.cp = :bool')
           ->setParameter('bool', 1);
         
        return $qb;
    }

}
