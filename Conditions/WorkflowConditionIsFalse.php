<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Workflow condition that evaluates to true if the provided input is false.
 *
 * Typically used together with WorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new WorkflowConditionVariable(
 *   'variable name',
 *   new WorkflowConditionIsFalse
 * );
 * ?>
 * </code>
 *
 */
class WorkflowConditionIsFalse implements WorkflowConditionInterface
{
    /**
     * Evaluates this condition with $value and returns true if it is false and false if it is not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return $value === false;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'is false';
    }
}

