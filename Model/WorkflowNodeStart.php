<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * An object of the WorkflowNodeStart class represents the one and only
 * tart node of a workflow. The execution of the workflow starts here.
 *
 * Creating an object of the Workflow class automatically creates the start node
 * for the new workflow. It can be accessed through the $startNode property of the
 * workflow.
 *
 * Incoming nodes: 0
 * Outgoing nodes: 1
 *
 * Example:
 * <code>
 * <?php
 * $workflow = new Workflow('Test');
 * $workflow->startNode->addOutNode(....some other node here ..);
 * ?>
 * </code>
 *
 */
class WorkflowNodeStart extends WorkflowNode
{
    /**
     * Constraint: The minimum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $minInNodes = 0;

    /**
     * Constraint: The maximum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $maxInNodes = 0;

    /**
     * Activates the sole output node.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(WorkflowExecution $execution)
    {
        $this->outNodes[0]->activate(
            $execution,
            $this,
            $execution->startThread()
        );
        $execution->info('Execution start !');

        return parent::execute($execution);
    }
}
