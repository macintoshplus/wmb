<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * This node implements the Exclusive Choice workflow pattern.
 *
 * The Exclusive Choice workflow pattern defines multiple possible paths
 * for the workflow of which exactly one is chosen based on the conditions
 * set for the out nodes.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 2..*
 *
 * This example displays how you can use an exclusive choice to select one of two
 * possible branches depending on the workflow variable 'value' which is read using
 * an input node.
 *
 * <code>
 * <?php
 * $workflow = new Workflow('Test');
 *
 * // wait for input into the workflow variable value.
 * $input = new WorkflowNodeInput(array('value' => new WorkflowConditionIsInt));
 * $workflow->startNode->addOutNode($input);
 *
 * // create the exclusive choice branching node
 * $choice = new WorkflowNodeExclusiveChoice;
 * $intput->addOutNode($choice);
 *
 * $branch1 = ....; // create nodes for the first branch of execution here..
 * $branch2 = ....; // create nodes for the second branch of execution here..
 *
 * // add the outnodes and set the conditions on the exclusive choice
 * $choice->addConditionalOutNode( new WorkflowConditionVariable( 'value',
 *                                                                  new WorkflowConditionGreaterThan(10 )),
 *                                $branch1 );
 * $choice->addConditionalOutNode( new WorkflowConditionVariable( 'value',
 *                                                                  new WorkflowConditionLessThan(11 )),
 *                                $branch2 );
 *
 * // Merge the two branches together and continue execution.
 * $merge = new WorkflowNodeSimpleMerge();
 * $merge->addInNode($branch1);
 * $merge->addInNode($branch2);
 * $merge->addOutNode($workflow->endNode);
 * ?>
 * </code>
 */
class WorkflowNodeExclusiveChoice extends WorkflowNodeConditionalBranch
{
    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to have. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minConditionalOutNodes = 2;

    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minActivatedConditionalOutNodes = 1;

    /**
     * Constraint: The maximum number of conditional outgoing nodes this node
     * may activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxActivatedConditionalOutNodes = 1;
}
