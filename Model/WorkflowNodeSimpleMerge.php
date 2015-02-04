<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * This node implements the Simple Merge (XOR-Join) workflow pattern.
 *
 * The Simple Merge workflow pattern is to be used to merge the possible paths that are defined
 * by a preceding Exclusive Choice. It is assumed that of these possible paths exactly one is
 * taken and no synchronization takes place.
 *
 * Use Case Example: After the payment has been performed by either credit card or bank
 * transfer, the order can be processed further.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 * This example displays how you can use a simple merge to tie together two different
 * execution paths from an exclusive choice into one.
 *
 * <code>
 * <?php
 * $workflow = new Workflow( 'Test' );
 *
 * // wait for input into the workflow variable value.
 * $input = new WorkflowNodeInput( array( 'value' => new WorkflowConditionIsInt ) );
 * $workflow->startNode->addOutNode( $input );
 *
 * // create the exclusive choice branching node
 * $choice = new WorkflowNodeExclusiveChoice;
 * $intput->addOutNode( $choice );
 *
 * $branch1 = ....; // create nodes for the first branch of execution here..
 * $branch2 = ....; // create nodes for the second branch of execution here..
 *
 * // add the outnodes and set the conditions on the exclusive choice
 * $choice->addConditionalOutNode( new WorkflowConditionVariable( 'value',
 *                                                                  new WorkflowConditionGreatherThan( 10 ) ),
 *                                $branch1 );
 * $choice->addConditionalOutNode( new WorkflowConditionVariable( 'value',
 *                                                                  new WorkflowConditionLessThan( 11 ) ),
 *                                $branch2 );
 *
 * // Merge the two branches together and continue execution.
 * $merge = new WorkflowNodeSimpleMerge();
 * $merge->addInNode( $branch1 );
 * $merge->addInNode( $branch2 );
 * $merge->addOutNode( $workflow->endNode );
 * ?>
 * </code>
 *
 */
class WorkflowNodeSimpleMerge extends WorkflowNodeMerge
{
    /**
     * Activate this node.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode $activatedFrom
     * @param int $threadId
     * @ignore
     */
    public function activate( WorkflowExecution $execution, WorkflowNode $activatedFrom = null, $threadId = 0 )
    {
        $parentThreadId = $execution->getParentThreadId( $threadId );

        if ( empty( $this->state['threads'] ) )
        {
            $this->state['threads'][] = $threadId;

            parent::activate( $execution, $activatedFrom, $parentThreadId );
        }
    }

    /**
     * Executes this node.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( WorkflowExecution $execution )
    {
        return $this->doMerge( $execution );
    }
}

