<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Abstract base class for boolean sets of conditions like AND, OR and XOR.
 *
 */
abstract class WorkflowConditionBooleanSet implements WorkflowConditionInterface
{
    /**
     * Array of WorkflowConditions
     *
     * @var array
     */
    protected $conditions;

    /**
     * String representation of the concatination.
     *
     * Used by the __toString() methods.
     *
     * @var string
     */
    protected $concatenation;

    /**
     * Constructs a new boolean set with the conditions $conditions.
     *
     * The format of $conditions must be array( WorkflowConditionInterface )
     *
     * @param array $conditions
     * @throws WorkflowDefinitionStorageException
     */
    public function __construct( array $conditions )
    {
        foreach ( $conditions as $condition )
        {
            if ( !$condition instanceof WorkflowConditionInterface )
            {
                throw new WorkflowDefinitionStorageException(
                  'Array does not contain (only) WorkflowConditionInterface objects.'
                );
            }

            $this->conditions[] = $condition;
        }
    }

    /**
     * Returns the conditions in this boolean set.
     *
     * @return WorkflowConditionInterface[]
     * @ignore
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        $string = '( ';

        foreach ( $this->conditions as $condition )
        {
            if ( $string != '( ' )
            {
                $string .= ' ' . $this->concatenation . ' ';
            }

            $string .= $condition;
        }

        return $string . ' )';
    }
}

