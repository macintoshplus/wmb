<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * The Loop node type is a special type of conditional branch node that has two
 * incoming nodes instead of just one. It is used to conveniently express loops.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 2..*
 *
 * The example below shows the equivalent of a for-loop that iterates the
 * variable i from 1 to 10:
 *
 * <code>
 * <?php
 * $workflow = new Workflow('IncrementingLoop');
 *
 * $set      = new WorkflowNodeVariableSet(array('i' => 1));
 * $step     = new WorkflowNodeVariableIncrement('i');
 * $break    = new WorkflowConditionVariable('i', new WorkflowConditionIsEqual(10));
 * $continue = new WorkflowConditionVariable('i', new WorkflowConditionIsLessThan(10));
 *
 * $workflow->startNode->addOutNode($set);
 *
 * $loop = new WorkflowNodeLoop;
 * $loop->addInNode($set);
 * $loop->addInNode($step);
 *
 * $loop->addConditionalOutNode($continue, $step);
 * $loop->addConditionalOutNode($break, $workflow->endNode);
 * ?>
 * </code>
 *
 */
class WorkflowNodeLoop extends WorkflowNodeConditionalBranch
{
    /**
     * Constraint: The minimum number of incoming nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minInNodes = 2;

    /**
     * Constraint: The maximum number of incoming nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxInNodes = false;

    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minOutNodes = 2;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxOutNodes = false;

    /**
     * Whether or not to start a new thread for a branch.
     *
     * @var bool
     */
    protected $startNewThreadForBranch = false;
}
