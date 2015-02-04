<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DefinitionSearchType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('version')
            ->add('parent')
            ->add('publishedAt')
            ->add('publishedBy')
            ->add('archivedAt')
            ->add('archivedBy')
            ->add('createdAt')
            ->add('createdBy')
            ->add('updatedAt')
            ->add('updatedBy')
            ->add('rolesForUpdate')
            ->add('rolesForUse')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JbNahan\Bundle\WorkflowManagerBundle\Entity\DefinitionSearch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jbnahan_bundle_workflowmanagerbundle_definitionsearch';
    }
}
