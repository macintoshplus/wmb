<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * Base class for nodes that merge multiple threads of execution.
 *
 */
abstract class WorkflowNodeMerge extends WorkflowNode
{
    /**
     * Constraint: The minimum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $minInNodes = 2;

    /**
     * Constraint: The maximum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $maxInNodes = false;

    /**
     * The state of this node.
     *
     * @var array
     */
    protected $state;

    /**
     * Prepares this node for activation.
     *
     * @param WorkflowExecution $execution
     * @param int $threadId
     * @throws WorkflowExecutionException
     */
    protected function prepareActivate( WorkflowExecution $execution, $threadId = 0 )
    {
        $parentThreadId = $execution->getParentThreadId( $threadId );

        if ( $this->state['siblings'] == -1 )
        {
            $this->state['siblings'] = $execution->getNumSiblingThreads( $threadId );
        }
        else
        {
            foreach ( $this->state['threads'] as $oldThreadId )
            {
                if ( $parentThreadId != $execution->getParentThreadId( $oldThreadId ) )
                {
                    throw new WorkflowExecutionException(
                      'Cannot synchronize threads that were started by different branches.'
                    );
                }
            }
        }

        $this->state['threads'][] = $threadId;
    }

    /**
     * Performs the merge by ending the incoming threads and
     * activating the outgoing node.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     */
    protected function doMerge( WorkflowExecution $execution )
    {
        foreach ( $this->state['threads'] as $threadId )
        {
            $execution->endThread( $threadId );
        }

        $this->activateNode( $execution, $this->outNodes[0] );
        $this->initState();

        return parent::execute( $execution );
    }

    /**
     * Initializes the state of this node.
     *
     * @ignore
     */
    public function initState()
    {
        parent::initState();

        $this->state = array( 'threads' => array(), 'siblings' => -1 );
    }
}

