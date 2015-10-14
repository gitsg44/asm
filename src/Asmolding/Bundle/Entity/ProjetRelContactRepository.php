<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ProjetRelContactRepository
 *
 * @author SGOURMELON
 */
class ProjetRelContactRepository extends EntityRepository {
     
    public function getContact($contact){
        
        $qb = $this->createQueryBuilder('prc');
        
         $qb
            ->where('prc.contact = :contact')
            ->setParameter('contact', $contact);
                 
        return $qb->getQuery()->getResult();
    }
}
