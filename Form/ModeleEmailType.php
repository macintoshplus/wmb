<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Modele Email Type
 */
class ModeleEmailType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'required'=>true,
                'constraints'=>[new NotBlank()]
                ])
            ->add('to', 'collection', [
                'type'=> 'text',
                'options'=>['required'=>false, 'constraints'=>[new NotBlank(), new Email()]],
                'required' => false,
                'cascade_validation'=>true,
                'allow_add'=>true,
                'prototype_name'=>'__name_value__',
                'allow_delete' => true,
                'error_bubbling'=>false,
                'constraints'=>[new NotBlank()]/*,
                'by_reference'=>false*/])
            ->add('from', 'text', [
                'required'=>true,
                'constraints'=>[new NotBlank(), new Email()]
                ])
            ->add('subject', 'text', [
                'required'=>true,
                'constraints'=>[new NotBlank()]
                ])
            ->add('body', 'textarea', [
                'required'=>true,
                'constraints'=>[new NotBlank()]
                ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
         $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                $list=['Default'];
                return $list;
            }
         ]);

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jb_nahan_modele_email';
    }
}
