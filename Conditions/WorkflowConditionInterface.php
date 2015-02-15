<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Interface for workflow conditions.
 *
 */
interface WorkflowConditionInterface
{
    /**
     * Evaluates this condition.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     */
    public function evaluate($value);

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     */
    public function __toString();
}
