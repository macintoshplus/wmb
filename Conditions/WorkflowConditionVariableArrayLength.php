<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Condition on array element count.
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 */
class WorkflowConditionVariableArrayLength implements WorkflowConditionInterface
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
            if (!is_array($value[$this->variableName])) {
                return false;
            }
            return $this->condition->evaluate(count($value[$this->variableName]));
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
        return 'count(' . $this->variableName . ') ' . $this->condition;
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
