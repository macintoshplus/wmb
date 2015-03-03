<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * This node implements the Cancel Case workflow pattern.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 0..1
 *
 */
class WorkflowNodeCancel extends WorkflowNodeEnd
{
    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minOutNodes = 0;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxOutNodes = 1;

    /**
     * Cancels the execution of this workflow.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode      $activatedFrom
     * @param int                  $threadId
     * @ignore
     */
    public function activate(WorkflowExecution $execution, WorkflowNode $activatedFrom = null, $threadId = 0)
    {
        $execution->info(sprintf('This execution will be cancelled by node (%s) id %d', ((null === $activatedFrom)? '':get_class($activatedFrom)), ((null === $activatedFrom)? '':$activatedFrom->getId())));
        $execution->cancel($this);
        $execution->info('This execution was cancelled');
    }
}
