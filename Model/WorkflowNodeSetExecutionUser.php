<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Twig_Environment;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;

/**
 * WorkflowNodeSetExecutionUser class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeSetExecutionUser extends WorkflowNode implements WorkflowNodeFormFieldAccessInterface
{
    protected $configuration = array(
        'form_internal_name'=>null,
        'field_internal_name'=>null);

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        if (!isset($configuration['form_internal_name'])) {
            $configuration['form_internal_name'] = null;
        }
        if (!isset($configuration['field_internal_name'])) {
            $configuration['field_internal_name'] = null;
        }
        parent::__construct($configuration);
    }


    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML(\DOMElement $element)
    {
        $configuration = array(
          'form_internal_name'     => $element->getAttribute('form_internal_name'),
          'field_internal_name' => $element->getAttribute('field_internal_name')
        );

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML(\DOMElement $element)
    {
        $element->setAttribute('form_internal_name', $this->configuration['form_internal_name']);
        $element->setAttribute('field_internal_name', $this->configuration['field_internal_name']);

    }
    
    /**
     * @return string
     */
    public function getFormInternalName()
    {
        return $this->configuration['form_internal_name'];
    }

    /**
     * @param string $formInternalName
     * @return WorkflowNodeSetExecutionUser
     */
    public function setFormInternalName($formInternalName)
    {
        if (!is_string($formInternalName)) {
            throw new BaseValueException('form_internal_name', $formInternalName, 'WorkflowNodeSetExecutionUser');
        }
        $this->configuration['form_internal_name'] = $formInternalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldInternalName()
    {
        return $this->configuration['field_internal_name'];
    }

    /**
     * @param string $fieldInternalName
     * @return WorkflowNodeSetExecutionUser
     */
    public function setFieldInternalName($fieldInternalName)
    {
        if (!is_string($fieldInternalName)) {
            throw new BaseValueException('field_internal_name', $fieldInternalName, 'WorkflowNodeSetExecutionUser');
        }
        $this->configuration['field_internal_name'] = $fieldInternalName;

        return $this;
    }

    /**
     * @param WorkflowExecution $execution
     * @return array|string
     */
    protected function getRolesFromForm(WorkflowExecution $execution)
    {
        $result = $execution->getVariable($this->configuration['form_internal_name']);
        return $result[0]->getAnswer($this->configuration['field_internal_name']);
    }

    /**
     * @param WorkflowExecution $execution
     * @return boolean
     */
    public function execute(WorkflowExecution $execution)
    {

        if (null === $this->configuration['form_internal_name']) {
            $err = "Unable to use this node if form internal name is not set";
            $execution->critical($err);
            throw new WorkflowExecutionException("Unable to use this node if form internal name is not set");
        }

        if (null === $this->configuration['field_internal_name']) {
            $err = "Unable to use this node if field internal name is not set";
            $execution->critical($err);
            throw new WorkflowExecutionException("Unable to use this node if field internal name is not set");
        }

        $roles = $this->getRolesFromForm($execution);
        if (null !== $roles) {
            if (!is_object($roles) || !($roles instanceof \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowRolesInterface)) {
                throw new WorkflowExecutionException("Unable to set user on execution");
            }

            $execution->setRoles(array($roles));
            $execution->info(sprintf("The role %s has been defined.", $roles->getUsername()));
        }

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);

    }

    public function verify()
    {
        parent::verify();

        if (null === $this->configuration['form_internal_name']) {
            throw new WorkflowInvalidWorkflowException('Node set execution user have not form internal name.');
        }

        if (null === $this->configuration['field_internal_name']) {
            throw new WorkflowInvalidWorkflowException('Node set execution user have not field internal name.');
        }
        
    }
}
