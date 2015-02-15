<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Wrapper that applies a condition to a workflow variable.
 *
 */
class WorkflowConditionVariable implements WorkflowConditionInterface
{
    /**
     * The name of the variable the condition is applied to.
     *
     * @var string
     */
    protected $variableName;

    /**
     * The condition that is applied to the variable.
     *
     * @var WorkflowConditionInterface
     */
    protected $condition;

    /**
     * Constructor.
     *
     * @param  string $variableName
     * @param  WorkflowConditionInterface $condition
     */
    public function __construct($variableName, WorkflowConditionInterface $condition)
    {
        $this->variableName = $variableName;
        $this->condition    = $condition;
    }

    /**
     * Evaluates this condition.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate($value)
    {
        if (is_array($value ) && isset( $value[$this->variableName])) {
            return $this->condition->evaluate($value[$this->variableName]);
        } else {
            return false;
        }
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->variableName . ' ' . $this->condition;
    }

    /**
     * Returns the name of the variable the condition is evaluated for.
     *
     * @return string
     * @ignore
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Returns the condition.
     *
     * @return WorkflowConditionInterface
     * @ignore
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
