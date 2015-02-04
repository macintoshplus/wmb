<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * This node implements the Discriminator workflow pattern.
 *
 * The Discriminator workflow pattern can be applied when the assumption made for the
 * Simple Merge workflow pattern does not hold. It can deal with merge situations where multiple
 * incoming branches may run in parallel.
 * It activates its outgoing node after being activated by the first incoming branch and then waits
 * for all remaining branches to complete before it resets itself. After the reset the Discriminator
 * can be triggered again.
 *
 * Use Case Example: To improve response time, an action is delegated to several distributed
 * servers. The first response proceeds the flow, the other responses are ignored.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 * This example creates a workflow that splits in two parallel threads which
 * are joined again using a WorkflowNodeDiscriminator.
 *
 * <code>
 * <?php
 * $workflow = new Workflow( 'Test' );
 *
 * $split = new WorkflowNodeParallelSplit();
 * $workflow->startNode->addOutNode( $split );
 * $nodeExec1 = ....; // create nodes for the first thread of execution here..
 * $nodeExec2 = ....; // create nodes for the second thread of execution here..
 *
 * $disc = new WorkflowNodeDiscriminator();
 * $disc->addInNode( $nodeExec1 );
 * $disc->addInNode( $nodeExec2 );
 * $disc->addOutNode( $workflow->endNode );
 * ?>
 * </code>
 */
class WorkflowNodeDiscriminator extends WorkflowNodeMerge
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
        $this->prepareActivate( $execution, $threadId );
        $this->setThreadId( $execution->getParentThreadId( $threadId ) );

        $numActivated = count( $this->state['threads'] );

        if ( $numActivated == 1 )
        {
            $this->activateNode( $execution, $this->outNodes[0] );
        }
        else if ( $numActivated == $execution->getNumSiblingThreads( $threadId ) )
        {
            parent::activate( $execution, $activatedFrom, $this->threadId );
        }

        $execution->endThread( $threadId );
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
        $this->initState();

        return parent::execute( $execution );
    }
}

