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
 * Description of FormSearchType
 *
 * @author SGOURMELON
 */
class FormSearchType extends AbstractType{
     
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('constraints' => array(new NotBlank,)));
            //->add('rechercher', 'submit');
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      //'data_class' => 'Asmolding\Bundle\Entity\Company',
        'validation_groups' => 'search'
    ));
  }

    public function getName() {
       return 'form_search'; 
    }
}
