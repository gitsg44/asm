<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of FormGlobalPlanType
 *
 * @author SGOURMELON
 */
class FormGlobalPlanType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $builder
        ->add('project', 'entity', array(
          'class' => 'AsmoldingBundle:Project',
          'property' => 'name'))
        ->add('mold', 'entity', array(
          'class' => 'AsmoldingBundle:Mold',
          'property' => 'name'))
        ->add('milestone', 'text')
        ->add('color', 'text')
        ->add('progress','percent', array('type' => 'integer'))
        ->add('sequence', 'integer')
        ->add('comment', 'checkbox')
        ->add('comment_txt', 'textarea')
        ->add('visible', 'checkbox');
    
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\GlobalPlan'
    ));
  }

    public function getName() {
       return 'form_globalPlan'; 
    }
}
