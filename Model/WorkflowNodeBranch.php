<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * Base class for nodes that branch multiple threads of execution.
 *
 */
abstract class WorkflowNodeBranch extends WorkflowNode
{
    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $minOutNodes = 2;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $maxOutNodes = false;

    /**
     * Whether or not to start a new thread for a branch.
     *
     * @var bool
     */
    protected $startNewThreadForBranch = true;

    /**
     * Activates this node's outgoing nodes.
     *
     * @param WorkflowExecution $execution
     * @param array                $nodes
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     */
    protected function activateOutgoingNodes( WorkflowExecution $execution, array $nodes )
    {
        $threadId           = $this->getThreadId();
        $numNodesToActivate = count( $nodes );

        foreach ( $nodes as $node )
        {
            if ( $this->startNewThreadForBranch )
            {
                $node->activate( $execution, $this, $execution->startThread( $threadId, $numNodesToActivate ) );
            }
            else
            {
                $node->activate( $execution, $this, $threadId );
            }
        }

        return parent::execute( $execution );
    }
}

