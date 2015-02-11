<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Condition that evaluates to true if the evaluated value is an instance of object.
 *
 * Typically used together with WorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new WorkflowConditionVariable(
 *   'variable name',
 *   new WorkflowConditionIsInstanceOf('MyClass')
 * );
 * ?>
 * </code>
 *
 */
class WorkflowConditionIsInstanceOf extends WorkflowConditionComparison
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = ' instance of ';

    /**
     * Evaluates this condition and returns true if $value is an object or false if not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate($value)
    {
        return ($value instanceof $this->value);
    }
}
