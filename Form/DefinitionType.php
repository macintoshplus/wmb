<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Definition Type
 */
class DefinitionType extends AbstractType
{

    /**
     * @var SecurityContext $security
     */
    private $security;

    /**
     * @param SecurityContext $security
     */
    public function __construct(SecurityContext $security)
    {
        $this->security = $security;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
    }
    
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if (false === $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }
        $form->add('rolesForUpdate', 'collection', array(
                'type'=> 'text',
                'options'=>array('required'=>false),
                'required' => false,
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
                'required' => false,
                'cascade_validation'=>false,
                'allow_add'=>true,
                'prototype_name'=>'__name_value__',
                'allow_delete' => true,
                'error_bubbling'=>false/*,
                'by_reference'=>false*/
            ));
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
        return 'jb_nahan_definition';
    }
}
