<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsAnything;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsBool;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionVariableArrayLength;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsGreaterThan;


use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException;

/**
 * WorkflowNodeControlForm class
 * Check if response is set or date is ok
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeControlForm extends WorkflowNodeConditionalBranch
{
    protected $configuration = array(
      'internal_name'=>null,
      'out_date'=>null,
      'condition' => array(),
      'else' => array()
    );

    protected $minInNodes = 1;

    protected $startNewThreadForBranch = false;

    protected $minActivatedConditionalOutNodes = 1;

    public function __construct(array $configuration)
    {
        parent::__construct($configuration);
    }

    /**
     * @param WorkflowNode $outNode
     * @param WorkflowNode $else
     * @return Workflow
     */
    public function addSelectOutNode(WorkflowNode $outNode, WorkflowNode $else)
    {
        $equal = new WorkflowConditionIsGreaterThan(0);
        $condition = new WorkflowConditionVariableArrayLength($this->getInternalName(), $equal);
        return parent::addConditionalOutNode($condition, $outNode, $else);
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
     * @return WorkflowNodeControlForm
     */
    public function setInternalName($internalName)
    {
        $this->configuration['internal_name'] = $internalName;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getOutDate()
    {
        return $this->configuration['out_date'];
    }

    /**
     * @param \DateTime $date
     * @return WorkflowNodeControlForm
     */
    public function setOutDate(\DateTime $date)
    {
        $this->configuration['out_date'] = $date;
        return $this;
    }

    /**
     * @param WorkflowExecution $execution
     */
    public function execute(WorkflowExecution $execution)
    {
        //Ne passe pas si la date n'est pas passé !
        if ($this->configuration['out_date'] > new \DateTime()) {
            $execution->debug("Date not pass : ".$this->configuration['out_date']->format('Y-m-d H:i:s'));
            return false;
        }

        return parent::execute($execution);

    }

    public function verify()
    {
        parent::verify();

        if (null === $this->getInternalName()) {
            throw new WorkflowInvalidWorkflowException('Node controle form has no form internal name.');
        }

        if (null === $this->configuration['out_date'] || !$this->configuration['out_date'] instanceof \DateTime) {
            throw new WorkflowInvalidWorkflowException(
                sprintf(
                    'Node control form "%s" have not out date.',
                    $this->getInternalName()
                )
            );
        }
    }
}
