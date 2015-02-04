<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Conditions;

/**
 * Abstract base class for comparison conditions.
 *
 */
abstract class WorkflowConditionComparison implements WorkflowConditionInterface
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = '';

    /**
     * The value that this condition compares against.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructs a new comparison condition.
     *
     * Implemenations will compare $value to the value provided to evaluate().
     *
     * @param  mixed  $value
     */
    public function __construct( $value = null )
    {
        $this->value = $value;
    }

    /**
     * Returns the value that this condition compares against.
     *
     * @return mixed
     * @ignore
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value that this condition compares against.
     *
     * @param mixed $value
     * @ignore
     */
    public function setValue( $value )
    {
        $this->value = $value;
    }

    /**
     * Returns the operator.
     *
     * @return string
     * @ignore
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->operator . ' ' . $this->value;
    }
}

