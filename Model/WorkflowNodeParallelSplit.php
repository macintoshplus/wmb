<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * This node implements the Parallel Split workflow pattern.
 *
 * The Parallel Split workflow pattern divides one thread of execution
 * unconditionally into multiple parallel threads of execution.
 *
 * Use Case Example: After the credit card specified by the customer has been successfully
 * charged, the activities of sending a confirmation email and starting the shipping process can
 * be executed in parallel.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 2..*
 *
 * This example creates a workflow that splits in two parallel threads which
 * are joined again using a WorkflowNodeDiscriminator.
 *
 * <code>
 * <?php
 * $workflow = new Workflow('Test');
 *
 * $split = new WorkflowNodeParallelSplit();
 * $workflow->startNode->addOutNode($split);
 * $nodeExec1 = ....; // create nodes for the first thread of execution here..
 * $nodeExec2 = ....; // create nodes for the second thread of execution here..
 *
 * $disc = new WorkflowNodeDiscriminator();
 * $disc->addInNode($nodeExec1);
 * $disc->addInNode($nodeExec2);
 * $disc->addOutNode($workflow->endNode);
 * ?>
 * </code>
 *
 */
class WorkflowNodeParallelSplit extends WorkflowNodeBranch
{
    /**
     * Activates all outgoing nodes.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(WorkflowExecution $execution)
    {
        return $this->activateOutgoingNodes($execution, $this->outNodes);
    }
}
