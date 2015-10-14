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
 * Description of FormPRCType
 *
 * @author SGOURMELON
 */
class FormPRCType extends AbstractType {
     
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $builder
        ->add('project', 'entity', array(
          'class' => 'AsmoldingBundle:Project',
          'property' => 'name',))
       ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\ProjetRelContact'
    ));
  }

    public function getName() {
       return 'form_prc'; 
    }
}
