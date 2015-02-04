<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Wrapper that applies a condition to two workflow variables.
 *
 */
class WorkflowConditionVariables implements WorkflowConditionInterface
{
    /**
     * The name of the first variable the condition is applied to.
     *
     * @var string
     */
    protected $variableNameA;

    /**
     * The name of the second variable the condition is applied to.
     *
     * @var string
     */
    protected $variableNameB;

    /**
     * The condition that is applied to the variable.
     *
     * @var WorkflowConditionInterface
     */
    protected $condition;

    /**
     * Constructor.
     *
     * @param  string $variableNameA
     * @param  string $variableNameB
     * @param  WorkflowConditionInterface $condition
     * @throws BaseValueException
     */
    public function __construct( $variableNameA, $variableNameB, WorkflowConditionInterface $condition )
    {
        if ( !$condition instanceof WorkflowConditionComparison )
        {
            throw new BaseValueException(
              'condition',
              $condition,
              'WorkflowConditionComparison'
            );
        }

        $this->variableNameA = $variableNameA;
        $this->variableNameB = $variableNameB;
        $this->condition     = $condition;
    }

    /**
     * Evaluates this condition.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        if ( is_array( $value ) &&
             isset( $value[$this->variableNameA] ) &&
             isset( $value[$this->variableNameB] ) )
        {
            $this->condition->setValue( $value[$this->variableNameB] );
            return $this->condition->evaluate( $value[$this->variableNameA] );
        }
        else
        {
            return false;
        }
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

    /**
     * Returns the names of the variables the condition is evaluated for.
     *
     * @return array
     * @ignore
     */
    public function getVariableNames()
    {
        return array( $this->variableNameA, $this->variableNameB );
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return sprintf(
          '%s %s %s',

          $this->variableNameA,
          $this->condition->getOperator(),
          $this->variableNameB
        );
    }
}

