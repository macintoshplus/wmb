<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * This node implements the Multi-Choice workflow pattern.
 *
 * The Multi-Choice workflow pattern defines multiple possible paths for the workflow of
 * which one or more are chosen. It is a generalization of the Parallel Split and
 * Exclusive Choice workflow patterns.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 2..*
 *
 * This example displays how you can use WorkflowNodeMultiChoice to activate one or more
 * branches depending on the input.  Note that an input value of 5 will start only branch 1
 * while an input value of 11 or more will start both branch1 and branch2.
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
 * $choice = new WorkflowNodeMultiChoice;
 * $intput->addOutNode($choice);
 *
 * $branch1 = ....; // create nodes for the first branch of execution here..
 * $branch2 = ....; // create nodes for the second branch of execution here..
 *
 * // add the outnodes and set the conditions on the exclusive choice
 * $choice->addConditionalOutNode( new WorkflowConditionVariable( 'value',
 *                                                                  new WorkflowConditionGreaterThan(1 )),
 *                                $branch1 );
 * $choice->addConditionalOutNode( new WorkflowConditionVariable( 'value',
 *                                                                  new WorkflowConditionGreaterThan(10 )),
 *                                $branch2 );
 *
 * // Merge the two branches together and continue execution.
 * $merge = new WorkflowNodeSynchronizingMerge();
 * $merge->addInNode($branch1);
 * $merge->addInNode($branch2);
 * $merge->addOutNode($workflow->endNode);
 * ?>
 * </code>
 *
 */
class WorkflowNodeMultiChoice extends WorkflowNodeConditionalBranch
{
}
