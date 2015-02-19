<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsAnything;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsBool;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionVariableArrayLength;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionVariable;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsGreaterThan;

/**
 * WorkflowNodeConditionalFormValue class
 * Check if response set is ok
 *
 * Use :
 * $node = new WorkflowNodeConditionalFormValue(array('form_internal_name'=>'form1', 'field_internal_name'=>'field1'));
 * $condition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test');
 * $outNode = new WorkflowNode...
 * $elseNode = new WorkflowNode...
 * $node->addSelectOutNode($outNode, $elseNode, $condition);
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeConditionalFormValue extends WorkflowNodeConditionalBranch implements WorkflowNodeFormFieldAccessInterface
{
    protected $configuration = array(
        'internal_name'=>null,
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
        if (!isset($configuration['internal_name'])) {
            $configuration['internal_name']='conditional_'.substr(uniqid(), -8);
        }
        parent::__construct($configuration);
    }

    /**
     * @param WorkflowNode $outNode
     * @param WorkflowNode $else
     * @return Workflow
     */
    public function addSelectOutNode(WorkflowNode $outNode, WorkflowNode $else, WorkflowConditionInterface $condition)
    {

        $conditionCompleted = new WorkflowConditionVariable($this->getInternalName(), $condition);
        return parent::addConditionalOutNode($conditionCompleted, $outNode, $else);
    }

    /**
     * return internal name
     * this name is use when ID for Type Form link
     * @return string
     */
    public function getInternalName()
    {
        return $this->configuration['internal_name'];
    }

    /**
     * @param string $internalName
     * @return WorkflowNodeReviewUniqueForm
     */
    // public function setInternalName($internalName)
    // {
    //     $this->configuration['internal_name'] = $internalName;

    //     return $this;
    // }
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
    public function execute(WorkflowExecution $execution)
    {
        //set la valeur
        $execution->setVariable($this->getInternalName(), $this->getValueFromForm($execution));
        //verifier la condition selon la valeur...
        return parent::execute($execution);
    }

    public function verify()
    {
        parent::verify();

        if (null === $this->getInternalName()) {
            throw new WorkflowInvalidWorkflowException('Node conditional form value has no internal name.');
        }
        if (null === $this->getFormInternalName()) {
            throw new WorkflowInvalidWorkflowException('Node conditional form value has no form internal name.');
        }
        if (null === $this->getFieldInternalName()) {
            throw new WorkflowInvalidWorkflowException('Node conditional form value has no field internal name.');
        }
    }
}
