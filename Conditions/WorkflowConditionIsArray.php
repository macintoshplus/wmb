<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;
/**
 * Condition that evaluates to true if the evaluated value is an array.
 *
 * Typically used together with WorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new WorkflowConditionVariable(
 *   'variable name',
 *   new WorkflowConditionIsArray
 * );
 * ?>
 * </code>
 *
 */
class WorkflowConditionIsArray extends WorkflowConditionType
{
    /**
     * Evaluates this condition and returns true if $value is an array or false if not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return is_array( $value );
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'is array';
    }
}

