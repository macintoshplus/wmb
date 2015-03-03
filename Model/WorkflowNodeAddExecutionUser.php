<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Twig_Environment;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;

/**
 * WorkflowNodeAddExecutionUser class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeAddExecutionUser extends WorkflowNode implements WorkflowNodeFormFieldAccessInterface
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
     * @return WorkflowNodeAddExecutionUser
     */
    public function setFormInternalName($formInternalName)
    {
        if (!is_string($formInternalName)) {
            throw new BaseValueException('form_internal_name', $formInternalName, 'WorkflowNodeAddExecutionUser');
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
     * @return WorkflowNodeAddExecutionUser
     */
    public function setFieldInternalName($fieldInternalName)
    {
        if (!is_string($fieldInternalName)) {
            throw new BaseValueException('field_internal_name', $fieldInternalName, 'WorkflowNodeAddExecutionUser');
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
            throw new \Exception($err);
        }

        if (null === $this->configuration['field_internal_name']) {
            $err = "Unable to use this node if field internal name is not set";
            $execution->critical($err);
            throw new \Exception($err);
        }

        $roles = $this->getRolesFromForm($execution);
        if (null !== $roles) {
            if (!is_object($roles) || !($roles instanceof \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowRolesInterface)) {
                $err = "Unable to set user on execution";
                $execution->critical($err);
                throw new \Exception($err);
            }
            //récupere les ancien roles
            $old = $execution->getRoles();
            if (!is_array($old)) {
                $old = array();
            }
            $old[]=$roles;

            $execution->setRoles($old);
            $execution->info(sprintf("The role %s has been added.", $roles->getUsername()));
        }

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);

    }

    /**
     * verify integrity
     */
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
