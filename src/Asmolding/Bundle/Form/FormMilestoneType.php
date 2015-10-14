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
 * Description of FormMilestoneType
 *
 * @author SGOURMELON
 */
class FormMilestoneType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $builder
        ->add('name', 'text')
        ->add('das', 'choice', array('choices' => array(
            'MA' =>'MA',
            'MN' =>'MN',
            'MO' =>'MO',
            'PR' =>'PR',
            'PS' =>'PS',
            'VPI' =>'VPI',
            'VPU' =>'VPU',)))
        ->add('solution', 'choice', array('choices' => array
            ('FRANCE' =>'FRANCE',
             'FRANCO-CHINE' =>'FRANCO-CHINE',
             'CHINE' =>'CHINE',
             'DIRECT CHINE' => 'DIRECT CHINE'),
            'empty_value' => 'Toutes'))
        ->add('sequence', 'integer')
        ->add('visible', 'checkbox');
    
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\Milestone'
    ));
  }

    public function getName() {
       return 'form_milestone'; 
    }
}
