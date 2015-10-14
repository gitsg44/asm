<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Form;

use Asmolding\Bundle\Entity\ContactRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of FormFilterPlanType
 *
 * @author SGOURMELON
 */
class FormFilterProjectPlanType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', 'entity', array(
              'class' => 'AsmoldingBundle:Company',
              'property' => 'name',
              'empty_value' => 'Choisissez...'))
            ->add('cp', 'entity', array(
              'class' => 'AsmoldingBundle:Contact',
              'property' => 'nameFirstname',
              'empty_value' => 'Choisissez...',
              'query_builder' => function(ContactRepository $er){
              return $er->getCps();}))
           ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'Asmolding\Bundle\Entity\Project'
      ));
    }

    public function getName() {
       return 'form_filter'; 
    }
}
