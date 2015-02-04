<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Visitor;

use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowVisitableInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNode;

/**
 * Collects all the nodes in a workflow in an array.
 *
 * @ignore
 */
class WorkflowVisitorNodeCollector extends WorkflowVisitor
{
    /**
     * Holds the start node object.
     *
     * @var WorkflowNodeStart
     */
    protected $startNode;

    /**
     * Holds the default end node object.
     *
     * @var WorkflowNodeEnd
     */
    protected $endNode;

    /**
     * Holds the finally node object.
     *
     * @var WorkflowNodeFinally
     */
    protected $finallyNode;

    /**
     * Flag that indicates whether the finally node has been visited.
     *
     * @var boolean
     */
    protected $finallyNodeVisited = false;

    /**
     * Holds the visited nodes.
     *
     * @var array(integer=>WorkflowNode)
     */
    protected $nodes = array();

    /**
     * Holds the sequence of node ids.
     *
     * @var integer
     */
    protected $nextId = 0;

    /**
     * Flag that indicates whether the node list has been sorted.
     *
     * @var boolean
     */
    protected $sorted = false;

    /**
     * Constructor.
     *
     * @param Workflow $workflow
     */
    public function __construct( Workflow $workflow )
    {
        parent::__construct();
        $workflow->accept( $this );
    }

    /**
     * Perform the visit.
     *
     * @param WorkflowVisitableInterface $visitable
     */
    protected function doVisit( WorkflowVisitableInterface $visitable )
    {
        if ( $visitable instanceof Workflow )
        {
            $visitable->startNode->setId( ++$this->nextId );
            $this->startNode = $visitable->startNode;

            $visitable->endNode->setId( ++$this->nextId );
            $this->endNode = $visitable->endNode;

            if ( count( $visitable->finallyNode->getOutNodes() ) > 0 )
            {
                $this->finallyNode = $visitable->finallyNode;
                $visitable->finallyNode->setId( ++$this->nextId );
            }
        }

        else if ( $visitable instanceof WorkflowNode )
        {
            if ( $visitable !== $this->startNode &&
                 $visitable !== $this->endNode &&
                 $visitable !== $this->finallyNode )
            {
                $id = ++$this->nextId;
                $visitable->setId( $id );
            }
            else
            {
                $id = $visitable->getId();
            }

            $this->nodes[$id] = $visitable;
        }
    }

    /**
     * Returns the collected nodes.
     *
     * @return array
     */
    public function getNodes()
    {
        if ( $this->finallyNode !== null && !$this->finallyNodeVisited )
        {
            $this->finallyNode->accept( $this );
            $this->finallyNode = true;
        }

        if ( !$this->sorted )
        {
            ksort( $this->nodes );
            $this->sorted = true;
        }

        return $this->nodes;
    }
}

