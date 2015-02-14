<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Visitor;

use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowVisitableInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNode;

use Countable;
use SplObjectStorage;

/**
 * Base class for Visitor
 */
class WorkflowVisitor implements Countable
{
    /**
     * Holds the visited nodes.
     *
     * @var SplObjectStorage
     */
    protected $visited;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->visited = new SplObjectStorage;
    }

    /**
     * Returns the number of visited nodes.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->visited);
    }

    /**
     * Visit the $visitable.
     *
     * Each node in the graph is visited once.
     *
     * @param WorkflowVisitableInterface $visitable
     * @return bool
     */
    public function visit(WorkflowVisitableInterface $visitable)
    {
        if ($visitable instanceof WorkflowNode) {
            if ($this->visited->contains($visitable)) {
                return false;
            }

            $this->visited->attach($visitable);
        }

        $this->doVisit($visitable);

        return true;
    }

    /**
     * Perform the visit.
     *
     * @param WorkflowVisitableInterface $visitable
     */
    protected function doVisit(WorkflowVisitableInterface $visitable)
    {
    }
}
