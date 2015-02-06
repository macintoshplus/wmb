<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
            ->add('name')
            ->add('to', 'text', array('required'=>true))
            ->add('from', 'text', array('required'=>true))
            ->add('subject', 'text', array('required'=>true))
            ->add('body', 'textarea', array('required'=>true))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // $resolver->setDefaults(array(
        //     'data_class' => 'JbNahan\Bundle\WorkflowManagerBundle\Entity\Definition'
        // ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jb_nahan_modele_email';
    }
}
