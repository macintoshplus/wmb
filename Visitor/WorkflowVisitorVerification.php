<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Visitor;

use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowVisitableInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNode;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFinally;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSetExecutionUser;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAddExecutionUser;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException;

/**
 * An implementation of the WorkflowVisitor interface that
 * verifies a workflow specification.
 *
 * This visitor should not be used directly but will be used by the
 * verify() method on the workflow.
 *
 * <code>
 * <?php
 * $workflow->verify();
 * ?>
 * </code>
 *
 * The verifier checks that:
 * - there is only one start node
 * - there is only one finally node
 * - each node satisfies the constraints of the respective node type
 *
 */
class WorkflowVisitorVerification extends WorkflowVisitor
{
    /**
     * Holds the number of start nodes encountered during visiting.
     *
     * @var integer
     */
    protected $numStartNodes = 0;

    /**
     * Holds the number of finally nodes encountered during visiting.
     *
     * @var integer
     */
    protected $numFinallyNodes = 0;

    /**
     * Perform the visit.
     *
     * @param WorkflowVisitableInterface $visitable
     */
    protected function doVisit(WorkflowVisitableInterface $visitable)
    {
        if ($visitable instanceof Workflow) {
            foreach ($visitable->nodes as $node) {
                if ($node instanceof WorkflowNodeStart &&
                    !$node instanceof WorkflowNodeFinally) {
                    $this->numStartNodes++;

                    if ($this->numStartNodes > 1) {
                        throw new WorkflowInvalidWorkflowException(
                          'A workflow may have only one start node.'
                        );
                    }
                }

                if ($node instanceof WorkflowNodeFinally) {
                    $this->numFinallyNodes++;

                    if ($this->numFinallyNodes > 1) {
                        throw new WorkflowInvalidWorkflowException(
                          'A workflow may have only one finally node.'
                        );
                    }
                }
            }
            //verif si toute les clées sont OK
            $internalname = array();
            $userinternalname = array();
            foreach ($visitable->nodes as $node) {
                if ($node instanceof )
            }
        }

        if ($visitable instanceof WorkflowNode) {
            $visitable->verify();
        }
    }
}
