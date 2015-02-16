<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;
/**
 * Abstract base class for workflow execution engine plugins.
 *
 */
abstract class WorkflowExecutionPlugin
{
    /**
     * Called after an execution has been started.
     *
     * @param WorkflowExecution $execution
     */
    public function afterExecutionStarted(WorkflowExecution $execution)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been suspended.
     *
     * @param WorkflowExecution $execution
     */
    public function afterExecutionSuspended(WorkflowExecution $execution)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been resumed.
     *
     * @param WorkflowExecution $execution
     */
    public function afterExecutionResumed(WorkflowExecution $execution)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been cancelled.
     *
     * @param WorkflowExecution $execution
     */
    public function afterExecutionCancelled(WorkflowExecution $execution)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has successfully ended.
     *
     * @param WorkflowExecution $execution
     */
    public function afterExecutionEnded(WorkflowExecution $execution)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a node is activated.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode      $node
     * @return bool true, when the node should be activated, false otherwise
     */
    public function beforeNodeActivated(WorkflowExecution $execution, WorkflowNode $node)
    {
    // @codeCoverageIgnoreStart
        return true;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a node has been activated.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode      $node
     */
    public function afterNodeActivated(WorkflowExecution $execution, WorkflowNode $node)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a node has been executed.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode      $node
     */
    public function afterNodeExecuted(WorkflowExecution $execution, WorkflowNode $node)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a new thread has been started.
     *
     * @param WorkflowExecution $execution
     * @param int                  $threadId
     * @param int                  $parentId
     * @param int                  $numSiblings
     */
    public function afterThreadStarted(WorkflowExecution $execution, $threadId, $parentId, $numSiblings)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a thread has ended.
     *
     * @param WorkflowExecution $execution
     * @param int                  $threadId
     */
    public function afterThreadEnded(WorkflowExecution $execution, $threadId)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a variable is set.
     *
     * @param  WorkflowExecution $execution
     * @param  string               $variableName
     * @param  mixed                $value
     * @return mixed the value the variable should be set to
     */
    public function beforeVariableSet(WorkflowExecution $execution, $variableName, $value)
    {
    // @codeCoverageIgnoreStart
        return $value;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a variable has been set.
     *
     * @param WorkflowExecution $execution
     * @param string               $variableName
     * @param mixed                $value
     */
    public function afterVariableSet(WorkflowExecution $execution, $variableName, $value)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a variable is unset.
     *
     * @param  WorkflowExecution $execution
     * @param  string               $variableName
     * @return bool true, when the variable should be unset, false otherwise
     */
    public function beforeVariableUnset(WorkflowExecution $execution, $variableName)
    {
    // @codeCoverageIgnoreStart
        return true;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a variable has been unset.
     *
     * @param WorkflowExecution $execution
     * @param string               $variableName
     */
    public function afterVariableUnset(WorkflowExecution $execution, $variableName)
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd
}
