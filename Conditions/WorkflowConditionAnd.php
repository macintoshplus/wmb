<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Boolean AND.
 *
 * An object of the WorkflowConditionAnd class represents a boolean AND expression. It
 * can hold an arbitrary number of WorkflowConditionInterface objects.
 *
 * <code>
 * <?php
 * $and = new WorkflowConditionAnd( array( $condition , ... ) );
 * ?>
 * </code>
 *
 */
class WorkflowConditionAnd extends WorkflowConditionBooleanSet
{
    /**
     * Textual representation of the concatenation.
     *
     * @var string
     */
    protected $concatenation = '&&';

    /**
     * Evaluates this condition with $value and returns true if the condition holds and false otherwise.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        foreach ( $this->conditions as $condition )
        {
            if ( !$condition->evaluate( $value ) )
            {
                return false;
            }
        }

        return true;
    }
}

