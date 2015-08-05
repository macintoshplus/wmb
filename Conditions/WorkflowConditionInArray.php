<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Condition that checks if a value is in an array.
 *
 * Typically used together with WorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new WorkflowConditionVariable(
 *   'variable name',
 *   new WorkflowConditionInArray(array(...))
 * );
 * ?>
 * </code>
 *
 */
class WorkflowConditionInArray extends WorkflowConditionComparison
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = 'in array';

    /**
     * Evaluates this condition with $value and returns true if it is false or false if it is not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate($value)
    {
        return in_array($value, $this->value);
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        $array = $this->value;
        $count = count($array);

        for ($i = 0; $i < $count; $i++) {
            $array[$i] = var_export($array[$i], true);
        }
        return $this->operator . '(' . join(', ', $array) . ')';
    }
}
