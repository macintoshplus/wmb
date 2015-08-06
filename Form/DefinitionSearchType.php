<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('isPublished', 'choice', array(
                'choices'=>array(true=>'Oui',false=>'Non'),
                'empty_value'=>'Tout',
                'empty_data'=>null,
                'required'=>false))
            ->add('publishedBy')
            ->add('archivedAt')
            ->add('isArchived', 'choice', array(
                'choices'=>array(true=>'Oui',false=>'Non'),
                'empty_value'=>'Tout',
                'empty_data'=>null,
                'required'=>false))
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
