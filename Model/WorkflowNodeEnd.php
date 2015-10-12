<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * An object of the WorkflowNodeEnd class represents an end node of a workflow.
 *
 * A workflow must have at least one end node. The execution of the workflow ends
 * when an end node is reached.
 * Creating an object of the Workflow class automatically creates a default end node for the new
 * workflow. It can be accessed through the getEndNode() method.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 0
 *
 * Example:
 * <code>
 * <?php
 * $workflow = new Workflow('Test');
 * // build up your workflow here... result in $node
 * $node = ...
 * $workflow->startNode->addOutNode(... some other node here ...);
 * $node->addOutNode($workflow->endNode);
 * ?>
 * </code>
 */
class WorkflowNodeEnd extends WorkflowNode
{
    protected $maxInNodes = false;
    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid.
     *
     * @var int
     */
    protected $minOutNodes = 0;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid.
     *
     * @var int
     */
    protected $maxOutNodes = 0;

    /**
     * Ends the execution of this workflow.
     *
     * @param WorkflowExecution $execution
     *
     * @return bool true when the node finished execution,
     *              and false otherwise
     * @ignore
     */
    public function execute(WorkflowExecution $execution)
    {
        $execution->end($this);
        $execution->info('Workflow Ended !');

        return parent::execute($execution);
    }
}
