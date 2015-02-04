<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 */
class Node
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $configuration;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $states;

    /**
     * @var \JbNahan\Bundle\WorkflowManagerBundle\Entity\Workflow
     */
    private $workflow;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->states = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return Node
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
     * Set configuration
     *
     * @param string $configuration
     * @return Node
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Get configuration
     *
     * @return string 
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Add states
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\ExecutionState $states
     * @return Node
     */
    public function addState(\JbNahan\Bundle\WorkflowManagerBundle\Entity\ExecutionState $states)
    {
        $this->states[] = $states;

        return $this;
    }

    /**
     * Remove states
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\ExecutionState $states
     */
    public function removeState(\JbNahan\Bundle\WorkflowManagerBundle\Entity\ExecutionState $states)
    {
        $this->states->removeElement($states);
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Set workflow
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Workflow $workflow
     * @return Node
     */
    public function setWorkflow(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Workflow $workflow = null)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get workflow
     *
     * @return \JbNahan\Bundle\WorkflowManagerBundle\Entity\Workflow 
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
    /**
     * @var string
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     * @return Node
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
