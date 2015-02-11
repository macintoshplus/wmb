<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Twig_Environment;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;

/**
 * WorkflowNodeSetExecutionUser class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeSetExecutionUser extends WorkflowNode
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
            throw new \Exception("Unable to use this node if form internal name is not set");
        }

        if (null === $this->configuration['field_internal_name']) {
            throw new \Exception("Unable to use this node if field internal name is not set");
        }

        $roles = $this->getRolesFromForm($execution);
        if (null !== $roles) {
            if (!($roles instanceof \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowRolesInterface)) {
                throw new \Exception("Unable to set user on execution (".get_class($roles).")");
            }

            $execution->setRoles(array($roles));
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
