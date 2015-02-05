<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VariableHandler
 */
class VariableHandler
{
    /**
     * @var integer
     */
    private $definition;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $variable;


    /**
     * Set definition
     *
     * @param integer $definition
     * @return VariableHandler
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get definition
     *
     * @return integer 
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return VariableHandler
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set variable
     *
     * @param string $variable
     * @return VariableHandler
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * Get variable
     *
     * @return string 
     */
    public function getVariable()
    {
        return $this->variable;
    }
}
