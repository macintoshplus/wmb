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
            ->add('to', 'text', array(
                'required'=>true,
                'constraints'=>array(new NotBlank(), new Email(array('groups' => array('ToEmail'))))
                ))
            ->add('from', 'text', array(
                'required'=>true,
                'constraints'=>array(new NotBlank(), new Email())
                ))
            ->add('subject', 'text', array(
                'required'=>true,
                'constraints'=>array(new NotBlank())
                ))
            ->add('body', 'textarea', array(
                'required'=>true,
                'constraints'=>array(new NotBlank())
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
         $resolver->setDefaults(array(
        //     'data_class' => 'JbNahan\Bundle\WorkflowManagerBundle\Entity\Definition'
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                $list=array('Default');
                if ('user' !== $data['to']) {
                    $list[]='ToEmail';
                }
                return $list;
            }
         ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jb_nahan_modele_email';
    }
}
