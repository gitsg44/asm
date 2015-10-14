<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of CompanyRepository
 *
 * @author SGOURMELON
 */
class CompanyRepository extends EntityRepository {
     
    public function searchCompaniesByName($name)
    {
        
        $qb = $this->createQueryBuilder('c');
        
         $qb->where('c.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->orderBy('c.name');
         
                 
        return $qb->getQuery()->getResult();
    }
    
    public function searchCompanies($name)
    {
        
        $qb = $this->createQueryBuilder('c');
        
         $qb->where('c.name LIKE :name')
            ->setParameter('name', $name . '%');
         
                 
        return $qb;
    }
    
    public function getAll(){
        
        $qb = $this->createQueryBuilder('c');
        
         $qb->orderBy('c.name', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
}
