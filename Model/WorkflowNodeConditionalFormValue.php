<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsAnything;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsBool;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionVariableArrayLength;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsGreaterThan;

/**
 * WorkflowNodeControlForm class
 * Check if response is set or date is ok
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeConditionalFormValue extends WorkflowNodeConditionalBranch
{
	protected $configuration = array(
	  	'form_internal_name'=>null,
        'field_internal_name'=>null,
      	'condition' => array(),
      	'else' => array()
    );

	protected $minInNodes = 1;

	protected $startNewThreadForBranch = false;
	
    protected $minActivatedConditionalOutNodes = 1;

	public function __construct(array $configuration)
	{
        parent::__construct( $configuration );
	}

	/**
	 * @param WorkflowNode $outNode
	 * @param WorkflowNode $else
	 * @return Workflow
	 */
	public function addSelectOutNode( WorkflowNode $outNode, WorkflowNode $else )
    {
    	$equal = new WorkflowConditionIsGreaterThan(0);
    	$condition = new WorkflowConditionVariableArrayLength($this->getInternalName(), $equal);
    	return parent::addConditionalOutNode($condition, $outNode, $else);
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
     * @return WorkflowNodeConditionalFormValue
     */
    public function setFormInternalName($formInternalName)
    {
        if (!is_string($formInternalName)) {
            throw new BaseValueException('form_internal_name', $formInternalName, 'WorkflowNodeConditionalFormValue');
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
     * @return WorkflowNodeConditionalFormValue
     */
    public function setFieldInternalName($fieldInternalName)
    {
        if (!is_string($fieldInternalName)) {
            throw new BaseValueException('field_internal_name', $fieldInternalName, 'WorkflowNodeConditionalFormValue');
        }
        $this->configuration['field_internal_name'] = $fieldInternalName;

        return $this;
    }

    /**
     * @param WorkflowExecution $execution
     * @return array|string
     */
    protected function getValueFromForm(WorkflowExecution $execution)
    {
        $result = $execution->getVariable($this->configuration['form_internal_name']);

        return $result[0]->getAnswer($this->configuration['field_internal_name']);
    }


	/**
	 * @param WorkflowExecution $execution
	 */
    public function execute( WorkflowExecution $execution )
    {
		//Ne passe pas si la date n'est pas passé !
    	$value = $this->getValueFromForm($execution);
    	//verifier la condition selon la valeur...

        return parent::execute( $execution );
    }

    public function verify() {
    	parent::verify();

    	if (null === $this->getFormInternalName()) {
    		throw new WorkflowInvalidWorkflowException('Node conditional form value has no form internal name.');
    	}
    	if (null === $this->getFieldInternalName()) {
    		throw new WorkflowInvalidWorkflowException('Node conditional form value has no field internal name.');
    	}
    }
}
