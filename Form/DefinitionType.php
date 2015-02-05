<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DefinitionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('rolesForUpdate', 'collection', array(
                'type'=> 'text',
                'options'=>array('required'=>false),
                'required' => true,
                'cascade_validation'=>false,
                'allow_add'=>true,
                'prototype_name'=>'__name_value__',
                'allow_delete' => true,
                'error_bubbling'=>false/*,
                'by_reference'=>false*/
            ))
            ->add('rolesForUse', 'collection', array(
                'type'=> 'text',
                'options'=>array('required'=>false),
                'required' => true,
                'cascade_validation'=>false,
                'allow_add'=>true,
                'prototype_name'=>'__name_value__',
                'allow_delete' => true,
                'error_bubbling'=>false/*,
                'by_reference'=>false*/
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JbNahan\Bundle\WorkflowManagerBundle\Entity\Definition'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jbnahan_bundle_workflowmanagerbundle_definition';
    }
}
