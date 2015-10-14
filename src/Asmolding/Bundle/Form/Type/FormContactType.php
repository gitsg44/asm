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
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of FormContactType
 *
 * @author SGOURMELON
 */
class FormContactType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
            ->add('company', 'entity', array(
              'class' => 'AsmoldingBundle:Company',
              'property' => 'name'))
            ->add('firstname', 'text')
            ->add('name', 'text')
            ->add('username', 'text')
            ->add('level','checkbox')
            ->add('cp','checkbox')
            ->add('email', 'email')
            ->add('phone', 'text', array('required' => false))
            ->add('fax', 'text', array('required' => false))
            ->add('mobile', 'text', array('required' => false))
            ->add('privatePhone', 'text', array('required' => false))
            ->add('department', 'choice', array('choices' => array
                ('ASM' => 'ASM',
                 'BE/R&D' => 'BUREAU D\'ETUDES / RECHERCHE & DEVELOPPEMENT',
                 'DIRECTION' => 'DIRECTION',
                 'SERVICE ACHAT' => 'SERVICE ACHAT',
                 'SERVICE COMMERCIAL' => 'SERVICE COMMERCIAL',
                 'SERVICE PRODUCTION' => 'SERVICE PRODUCTION',
                 'SERVICE PROJETS' => 'SERVICE PROJETS',
                 ),
                'empty_value' => 'Choisissez une fonction',
                'required' => false));
    
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\Contact'
    ));
  }

    public function getName() {
       return 'form_contact'; 
    }
}
