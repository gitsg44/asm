<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Form\Type;

use Asmolding\Bundle\Entity\MoldRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of FormFilterPlanType
 *
 * @author SGOURMELON
 */
class FormFilterMoldPlanType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project', 'entity', array(
              'class' => 'AsmoldingBundle:Project',
              'property' => 'name',
              'empty_value' => 'Choisissez...'))
            ->add('das', 'choice', array(
              'choices' => array(
                'MA' => 'MA',
                'MN' => 'MN',
                'MO' => 'MO',
                'PR' => 'PR',
                'PS' => 'PS',
                'VPI' => 'VPI',
                'VPU' => 'VPU'),
                'empty_value' => 'Choisissez...'))
            ->add('solution', 'choice', array(
              'choices' => array(
                'FRANCE' => 'FRANCE',
                'FRANCO-CHINE' => 'FRANCO-CHINE',
                'CHINE' => 'CHINE',
                'DIRECT CHINE' => 'DIRECT CHINE'),
              'empty_value' => 'Choisissez...'))
           ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'Asmolding\Bundle\Entity\Mold'
      ));
    }

    public function getName() {
       return 'form_filter'; 
    }
}
