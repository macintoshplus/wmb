<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExecutionState
 */
class ExecutionState
{
    /**
     * @var string
     */
    private $nodeState;

    /**
     * @var string
     */
    private $nodeActivatedFrom;

    /**
     * @var integer
     */
    private $nodeThreadId;

    /**
     * @var \JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution
     */
    private $execution;
    
    /**
     * @var integer
     */
    private $node;


    /**
     * Set nodeState
     *
     * @param string $nodeState
     * @return ExecutionState
     */
    public function setNodeState($nodeState)
    {
        $this->nodeState = $nodeState;

        return $this;
    }

    /**
     * Get nodeState
     *
     * @return string 
     */
    public function getNodeState()
    {
        return $this->nodeState;
    }

    /**
     * Set nodeActivatedFrom
     *
     * @param string $nodeActivatedFrom
     * @return ExecutionState
     */
    public function setNodeActivatedFrom($nodeActivatedFrom)
    {
        $this->nodeActivatedFrom = $nodeActivatedFrom;

        return $this;
    }

    /**
     * Get nodeActivatedFrom
     *
     * @return string 
     */
    public function getNodeActivatedFrom()
    {
        return $this->nodeActivatedFrom;
    }

    /**
     * Set nodeThreadId
     *
     * @param integer $nodeThreadId
     * @return ExecutionState
     */
    public function setNodeThreadId($nodeThreadId)
    {
        $this->nodeThreadId = $nodeThreadId;

        return $this;
    }

    /**
     * Get nodeThreadId
     *
     * @return integer 
     */
    public function getNodeThreadId()
    {
        return $this->nodeThreadId;
    }

    /**
     * Set execution
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution $execution
     * @return ExecutionState
     */
    public function setExecution(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution $execution)
    {
        $this->execution = $execution;

        return $this;
    }

    /**
     * Get execution
     *
     * @return \JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution 
     */
    public function getExecution()
    {
        return $this->execution;
    }


    /**
     * Set node
     *
     * @param integer $node
     * @return ExecutionState
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return integer 
     */
    public function getNode()
    {
        return $this->node;
    }
}
