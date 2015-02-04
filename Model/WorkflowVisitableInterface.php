<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitor;


/**
 * WorkflowVisitableInterface
 */
interface WorkflowVisitableInterface
{
    /**
     * Accepts the visitor.
     *
     * @param WorkflowVisitor $visitor
     */
    public function accept(WorkflowVisitor $visitor);
}
