<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * ExecutionSearch Type
 */
class ExecutionSearchType extends AbstractType
{

    /**
     * @var SecurityContext $security
     */
    //private $security;

    /**
     * @param SecurityContext $security
     */
    /*public function __construct(SecurityContext $security)
    {
        $this->security = $security;
    }*/

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('required'=>false))
            ->add('definition', 'text', array('required'=>false))
            ->add('startedAt', 'date', array(
                'required'=>false,
                'format'=>'dd/MM/yyyy',
                'input'=>'datetime',
                'widget'=>'single_text',
                'attr'=>array('class'=>'datepicker', 'autocomplete'=>'OFF')
            ))
            ->add('startedAtEnd', 'date', array(
                'required'=>false,
                'format'=>'dd/MM/yyyy',
                'input'=>'datetime',
                'widget'=>'single_text',
                'attr'=>array('class'=>'datepicker', 'autocomplete'=>'OFF')
            ))
            ->add('canceledAt', 'date', array(
                'required'=>false,
                'format'=>'dd/MM/yyyy',
                'input'=>'datetime',
                'widget'=>'single_text',
                'attr'=>array('class'=>'datepicker', 'autocomplete'=>'OFF')
            ))
            ->add('isCanceled', 'choice', array(
                'choices'=>array(true=>'Oui',false=>'Non'),
                'empty_value'=>'Tout',
                'empty_data'=>null,
                'required'=>false))
            ->add('canceledAtEnd', 'date', array(
                'required'=>false,
                'format'=>'dd/MM/yyyy',
                'input'=>'datetime',
                'widget'=>'single_text',
                'attr'=>array('class'=>'datepicker', 'autocomplete'=>'OFF')
            ))
            ->add('isEnded', 'choice', array(
                'choices'=>array(true=>'Oui',false=>'Non'),
                'empty_value'=>'Tout',
                'empty_data'=>null,
                'required'=>false))
            ->add('endAt', 'date', array(
                'required'=>false,
                'format'=>'dd/MM/yyyy',
                'input'=>'datetime',
                'widget'=>'single_text',
                'attr'=>array('class'=>'datepicker', 'autocomplete'=>'OFF')
            ))
            ->add('endAtEnd', 'date', array(
                'required'=>false,
                'format'=>'dd/MM/yyyy',
                'input'=>'datetime',
                'widget'=>'single_text',
                'attr'=>array('class'=>'datepicker', 'autocomplete'=>'OFF')
            ))
        ;
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JbNahan\Bundle\WorkflowManagerBundle\Entity\ExecutionSearch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jb_nahan_execution_search';
    }
}
