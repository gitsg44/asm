<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Form\Type;

use Asmolding\Bundle\Entity\GeneralPlanRepository;
use Asmolding\Bundle\Entity\MilestoneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of FormGeneralPlanType
 *
 * @author SGOURMELON
 */
class FormGeneralPlanType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    
    $todayYear = date('Y');
    $nextYear = date('Y', strtotime('+1 year'));
    $years[] = array($todayYear => $todayYear, $nextYear => $nextYear);
            
    $builder
        ->add('project', 'entity', array(
          'class' => 'AsmoldingBundle:Project',
          'property' => 'name'))
        ->add('mold', 'entity', array(
          'class' => 'AsmoldingBundle:Mold',
          'property' => 'name'))
        ->add('milestone', 'entity', array(
          'class' => 'AsmoldingBundle:Milestone',
          'property' => 'name',
          'query_builder' => function(MilestoneRepository $er) {
                        return $er->getOrdered();}))
        ->add('pweek', 'text')
        ->add('pday', 'text')
        ->add('pyear', 'choice', array('choices' => $years))
        ->add('rweek', 'text')
        ->add('rday', 'text')
        ->add('ryear', 'text')
        ->add('ryear', 'choice', array('choices' => $years))
        ;
    
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\GeneralPlan',
      'validation_groups' => function(FormInterface $form) {
            $type = $form->get('test')->getData();
        
            if ($type == 'previsionnel') {
                return array('previsionnel');
            } else if ($type == 'reel'){
                return array('reel');
            } else {
                return array('all');
            }
        
        },

    ));
  }

    public function getName() {
       return 'form_generalPlan'; 
    }
}
