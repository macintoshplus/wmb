<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Visitor;

/**
 * An implementation of the WorkflowVisitor interface that
 * resets all the nodes of a workflow.
 *
 * This visitor should not be used directly but will be used by the
 * reset() method on the workflow.
 *
 * <code>
 * <?php
 * $workflow->reset();
 * ?>
 * </code>
 *
 */
class WorkflowVisitorReset extends WorkflowVisitor
{
    /**
     * Perform the visit.
     *
     * @param WorkflowVisitableInterface $visitable
     */
    protected function doVisit( WorkflowVisitableInterface $visitable )
    {
        if ( $visitable instanceof WorkflowNode )
        {
            $visitable->initState();
        }
    }
}
