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
 * Description of FormCompanyType
 *
 * @author SGOURMELON
 */
class FormCompanyType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $builder
      ->add('name', 'text')
        ->add('name2', 'text')
        ->add('legalform', 'text')
        ->add('legalform2', 'text')
        ->add('country', 'choice', array('choices' => array(
                'FR' => 'FRANCE', 
                'DE' => 'ALLEMAGNE', 
                'BE' => 'BELGIQUE',
                'RU' => 'RUSSIE', 
                'PT' => 'PORTUGAL',
                'ES' => 'ESPAGNE',
                'IR' => 'IRAN',
                'HK' => 'HONG-KONG', 
                'MC' => 'MONACO', 
                'CN' => 'CHINE',
                'SK' => 'SLOVAQUIE', 
            )))
        ->add('country2', 'choice', array('choices' => array(
                'FR' => 'FRANCE', 
                'DE' => 'ALLEMAGNE', 
                'BE' => 'BELGIQUE',
                'RU' => 'RUSSIE', 
                'PT' => 'PORTUGAL',
                'ES' => 'ESPAGNE',
                'IR' => 'IRAN',
                'HK' => 'HONG-KONG', 
                'MC' => 'MONACO', 
                'CN' => 'CHINE',
                'SK' => 'SLOVAQUIE', 
            ),
                'empty_value' => ''))
        ->add('address', 'text')
        ->add('address2', 'text')
        ->add('zip', 'text')
        ->add('zip2', 'text')
        ->add('city','text')
        ->add('city2', 'text')
        ->add('phone', 'text', array('required' => false))
        ->add('phone2', 'text', array('required' => false))
        ->add('fax', 'text', array('required' => false))
        ->add('fax2', 'text', array('required' => false))
        ->add('email', 'email')
        ->add('email2', 'email')
        ->add('www', 'text', array('required' => false))
        ->add('www2', 'text', array('required' => false))
        ->add('branche', 'choice', array('choices' => array(
                '0' => 'INVENTEUR FOU', 
                '1' => 'GAMMISTE', 
                '2' => 'RANG 1 AUTO',
                '3' => 'BE-R&D', 
                '4' => 'INJECTEUR HORS AUTO',
                '5' => 'RANG 2 AUTO',
                '6' => 'PACK/EMBALLAGE',
                '7' => 'DESIGNER', 
                '8' => 'USINEUR MOULISTE', 
                '9' => 'PROTOTYPISTE',
            ),
                'empty_value' => 'Choisissez un segment client'))
        ->add('level', 'checkbox')
        ->add('reglevel', 'checkbox');
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Asmolding\Bundle\Entity\Company',
      'validation_groups' => array('Default')  
    ));
  }

    public function getName() {
       return 'form_company'; 
    }
}
