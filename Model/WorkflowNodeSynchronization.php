<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * This node implements the Synchronization (AND-Join) workflow pattern.
 *
 * The Synchronization workflow pattern synchronizes multiple parallel threads of execution
 * into a single thread of execution.
 *
 * Workflow execution continues once all threads of execution that are to be synchronized have
 * finished executing (exactly once).
 *
 * Use Case Example: After the confirmation email has been sent and the shipping process has
 * been completed, the order can be archived.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 */
class WorkflowNodeSynchronization extends WorkflowNodeMerge
{
    /**
     * Activate this node.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode $activatedFrom
     * @param int $threadId
     * @ignore
     */
    public function activate(WorkflowExecution $execution, WorkflowNode $activatedFrom = null, $threadId = 0)
    {
        $this->prepareActivate($execution, $threadId);
        parent::activate($execution, $activatedFrom, $execution->getParentThreadId($threadId));
    }

    /**
     * Executes this node.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(WorkflowExecution $execution)
    {
        if (count($this->state['threads']) == $this->state['siblings']) {
            return $this->doMerge($execution);
        } else {
            return false;
        }
    }
}
