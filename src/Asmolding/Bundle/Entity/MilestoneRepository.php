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
class MilestoneRepository extends EntityRepository{
    
    public function getAll(){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->orderBy('m.sequence', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getAllVisible(){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->where('m.visible = :bool')
           ->setParameter(':bool', 1)
           ->orderBy('m.sequence', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getChine(){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->where('m.solution = :solution')
            ->setParameter('solution', 'CHINE')
            ->orderBy('m.sequence', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getFrancoChine(){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->where('m.solution = :solution')
            ->setParameter('solution', 'FRANCO-CHINE')
            ->orderBy('m.sequence', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getOneBySequence($sequence){
        
        $qb = $this->createQueryBuilder('m');
        
         $qb->where('m.sequence = :sequence')
            ->setParameter('sequence', $sequence);
            
                 
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getOrdered(){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->orderBy('m.sequence', 'ASC');
                 
        return $qb;
    }
    
    public function getOrderedByDas($das){
        
        $qb = $this->createQueryBuilder('m');
        
        $qb->where('m.das = :das')
           ->setParameter(':das', $das)
           ->orderBy('m.sequence', 'ASC');
                 
        return $qb;
    }
    
    
}
