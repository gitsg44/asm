<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of FormGeneralPlanType
 *
 * @author SGOURMELON
 */
class FormProgressType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    for($i=1;$i<=53;$i++)
    {
        $weeknum[] = $i;
    }
    
    for($i=1;$i<=6;$i++)
    {
        $daynum[] = $i;
    }

    $builder
        ->add('week', 'choice', array('choices' => $weeknum, 'label' => 'N° semaine'))
        ->add('day', 'choice', array('choices' => $daynum, 'label' => 'N° jour'))
        ;
    
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\GeneralPlan',
    ));
  }

    public function getName() {
       return 'form_progress'; 
    }
}
