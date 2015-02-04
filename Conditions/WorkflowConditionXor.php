<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Boolean XOR.
 *
 * An object of the WorkflowConditionXor class represents a boolean XOR expression. It
 * can hold an arbitrary number of WorkflowConditionInterface objects.
 *
 * <code>
 * <?php
 * $xor = new WorkflowConditionXor( array ( $condition , ... ) );
 * ?>
 * </code>
 *
 */
class WorkflowConditionXor extends WorkflowConditionBooleanSet
{
    /**
     * Textual representation of the concatenation.
     *
     * @var string
     */
    protected $concatenation = 'XOR';

    /**
     * Evaluates this condition with $value and returns true if the condition holds and false otherwise.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        $result = false;

        foreach ( $this->conditions as $condition )
        {
            if ( $condition->evaluate( $value ) )
            {
                if ( $result )
                {
                    return false;
                }

                $result = true;
            }
        }

        return $result;
    }
}

