<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of GeneralPlanRepository
 *
 * @author SGOURMELON
 */
class GeneralPlanRepository extends EntityRepository{
    
    public function getOneByMoldAndMilestone($mold, $milestone){
        
        $qb = $this->createQueryBuilder('gp');
        
        $qb->where('gp.mold = :mold')
           ->andWhere('gp.milestone = :milestone')
           ->setParameter('mold', $mold)
           ->setParameter('milestone', $milestone);
              
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getAll(){
        
        $qb = $this->createQueryBuilder('gp');
        
        $qb->join('gp.milestone', 'milestone')

           ->addOrderBy('milestone.sequence', 'ASC');
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getGeneralPlansByUser($user){
        
        $qb = $this->createQueryBuilder('gp');
        
        $qb->join('gp.project', 'p')
           ->join('p.projetRelContacts', 'prc')
           ->where('prc.contact = :user')
           ->setParameter('user', $user);
            
                 
        return $qb->getQuery()->getResult();
    }
    
    public function getRecentUpdateTime(){
        
        $qb = $this->createQueryBuilder('gp');
        
        $qb->orderBy('gp.updateTime', 'DESC')
           ->setMaxResults(1);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getRecentUpdateTimeByProject($project){
        
        $qb = $this->createQueryBuilder('gp');
        
        $qb->where('gp.project = :project')
           ->setParameter(':project', $project)
           ->orderBy('gp.updateTime', 'DESC')
           ->setMaxResults(1);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getRecentUpdateTimeByClient($company){
        
        $qb = $this->createQueryBuilder('gp');
        
        $qb->join('gp.project', 'p')
           ->join('p.company', 'c')
           ->where('c = :company')
           ->setParameter(':company', $company)
           ->orderBy('gp.updateTime', 'DESC')
           ->setMaxResults(1);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}
