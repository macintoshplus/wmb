<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Boolean OR.
 *
 * An object of the WorkflowConditionOr class represents a boolean OR expression. It can
 * hold an arbitrary number of WorkflowConditionInterface objects.
 *
 * <code>
 * <?php
 * $or = new WorkflowConditionOr(array ($condition , ...));
 * ?>
 * </code>
 *
 */
class WorkflowConditionOr extends WorkflowConditionBooleanSet
{
    /**
     * Textual representation of the concatenation.
     *
     * @var string
     */
    protected $concatenation = '||';

    /**
     * Evaluates this condition with $value and returns true if the condition holds and false otherwise.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate($value)
    {
        foreach ($this->conditions as $condition) {
            if ($condition->evaluate($value)) {
                return true;
            }
        }

        return false;
    }
}
