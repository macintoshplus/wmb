<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Condition that evaluates to true if the provided value is greater than or equal to the reference value.
 *
 * Typically used together with WorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new WorkflowConditionVariable(
 *   'variable name',
 *   new WorkflowConditionIsEqualOrGreatherThan($comparisonValue)
 * );
 * ?>
 * </code>
 *
 */
class WorkflowConditionIsEqualOrGreaterThan extends WorkflowConditionComparison
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = '>=';

    /**
     * Evaluates this condition with $value and returns true if $value is greather than
     * or equal to the reference value or false if not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate($value)
    {
        return $value >= $this->value;
    }
}
