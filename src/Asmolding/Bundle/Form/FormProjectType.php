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
 * Description of FormProjectType
 *
 * @author SGOURMELON
 */
class FormProjectType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $builder
        ->add('company', 'entity', array(
          'class' => 'AsmoldingBundle:Company',
          'property' => 'name',
          'empty_value' => 'Choisissez un client'))
        ->add('num1', 'text')
        ->add('num2', 'text')
        //->add('num', new FormNumProjectType())
        ->add('name', 'text')
        ->add('description', 'textarea')
        ->add('planstart', 'date', array(
            'input' => 'datetime', 
            'widget' => 'choice',
            'format' => 'd / MMMM / y',
            'empty_value' => array('year' => 'Année', 'month' => 'Mois', 'day' => 'Jour'),
            ))
         ->add('image', new FormImageType(), array('required' => false));
        
     
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\Project'
    ));
  }

    public function getName() {
       return 'form_project'; 
    }
}
