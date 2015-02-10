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
