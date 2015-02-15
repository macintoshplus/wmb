<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Condition that always evaluates to true.
 *
 * Typically used together with WorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new WorkflowConditionVariable(
 *   'variable name',
 *   new WorkflowConditionIsAnything
 * );
 * ?>
 * </code>
 *
 */
class WorkflowConditionIsAnything extends WorkflowConditionType
{
    /**
     * Returns true.
     *
     * @param  mixed $value
     * @return boolean true
     * @ignore
     */
    public function evaluate($value)
    {
        return true;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'is anything';
    }
}
