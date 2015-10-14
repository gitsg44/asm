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
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of ChangePasswordType
 *
 * @author SGOURMELON
 */
class ChangePasswordType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', 'password');
        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'constraints' => array(new NotBlank),
            'invalid_message' => 'Les mots de passe doivent correspondre.',
            'required' => true,
            'first_options'  => array('label' => 'Mot de passe'),
            'second_options' => array('label' => 'Confirmez le mot de passe')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Asmolding\Bundle\Entity\Contact',
            'validation_groups' => array('changePassword'),
        ));
    }

    public function getName()
    {
        return 'change_password';
    }

}
