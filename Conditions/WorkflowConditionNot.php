<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Boolean NOT.
 *
 * An object of the WorkflowConditionNot decorates an WorkflowConditionInterface object
 * and negates its expression.
 *
 * <code>
 * <?php
 * $notNondition = new WorkflowConditionNot( $condition ) ;
 * ?>
 * </code>
 *
 */
class WorkflowConditionNot implements WorkflowConditionInterface
{
    /**
     * Holds the expression to negate.
     *
     * @var WorkflowConditionInterface
     */
    protected $condition;

    /**
     * Constructs a new not condition on $condition.
     *
     * @param  WorkflowConditionInterface $condition
     */
    public function __construct( WorkflowConditionInterface $condition )
    {
        $this->condition = $condition;
    }

    /**
     * Evaluates this condition with the value $value and returns true if the condition holds.
     *
     * If the condition does not hold false is returned.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return !$this->condition->evaluate( $value );
    }

    /**
     * Returns the condition that is negated.
     *
     * @return WorkflowConditionInterface
     * @ignore
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return '! ' . $this->condition;
    }
}

