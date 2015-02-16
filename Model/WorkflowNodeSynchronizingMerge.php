<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * This node implements the Synchronizing Merge workflow pattern.
 *
 * The Synchronizing Merge workflow pattern is to be used to synchronize multiple parallel
 * threads of execution that are activated by a preceding Multi-Choice.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 * This example displays how you can use WorkflowNodeMultiChoice to activate one or more
 * branches depending on the input and how you can use a synchronizing merge to merge them
 * together again. Execution will not contiue until all activated branches have been completed.
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
 * $input->addOutNode($choice);
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
 */
class WorkflowNodeSynchronizingMerge extends WorkflowNodeSynchronization
{
}
