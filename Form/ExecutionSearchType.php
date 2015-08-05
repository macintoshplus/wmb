<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use JbNahan\Bundle\WorkflowManagerBundle\Manager\DefinitionManager;
use JbNahan\Bundle\WorkflowManagerBundle\Entity\DefinitionSearch;
use JbNahan\Bundle\WorkflowManagerBundle\Form\DataTransformer\DefinitionToNumberTransformer;

/**
 * ExecutionSearch Type
 */
class ExecutionSearchType extends AbstractType
{

    /**
     * @var AuthorizationChecker $security
     */
    private $security;

    private $defManager;

    /**
     * @param AuthorizationChecker $security
     */
    public function __construct(AuthorizationChecker $security, DefinitionManager $defManager)
    {
        $this->security = $security;
        $this->defManager = $defManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', array('required'=>false))
            ->add('name', 'text', array('required'=>false))
            ->add('suspendedStep', 'text', array('required'=>false))
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
                'label'=>'Is canceled ?',
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
                'label'=>'Is ended ?',
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

        //$form = $event->getForm();
        $param = new DefinitionSearch();

        $roles = $this->security->getToken()->getUser()->getRoles();

        if (!in_array('ROLE_ADMIN', $roles)) {
            $param->setRolesForUse($roles);
        }
        $qb = $this->defManager->getQbDefinition($param);
        $transform = new DefinitionToNumberTransformer($this->defManager);
        $builder
            ->add($builder->create('definition', 'entity', array(
                    'required'=>false,
                    'class'=>'JbNahanWorkflowManagerBundle:Definition',
                    'query_builder'=>$qb,
                    'empty_value'=>'Tout',
                    'empty_data'=>null,
                ))->addModelTransformer($transform)
            );
        
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
