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
 * Description of FormMoldType
 *
 * @author SGOURMELON
 */
class FormMoldType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $builder
        ->add('company', 'entity', array(
          'class' => 'AsmoldingBundle:Company',
          'property' => 'name'))
        ->add('project', 'entity', array(
          'class' => 'AsmoldingBundle:Project',
          'property' => 'name'))
        ->add('name', 'text')
        ->add('num1', 'text')
        ->add('das', 'choice', array('choices' => array
            ('MA' =>'MA',
             'MN' =>'MN',
             'MO' =>'MO',
             'PR' =>'PR',
             'PS' =>'PS',
             'VPI' =>'VPI',
             'VPU' =>'VPU',)))
        ->add('num2', 'text')
        ->add('description', 'textarea')
        ->add('solution', 'choice', array('choices' => array
            ('FRANCE' =>'FRANCE',
             'FRANCO-CHINE' =>'FRANCO-CHINE',
             'CHINE' =>'CHINE',
             'DIRECT CHINE' => 'DIRECT CHINE')))
        ->add('image', new FormImageType(), array('required' => false));
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\Mold'
    ));
  }

    public function getName() {
       return 'form_mold'; 
    }
}
