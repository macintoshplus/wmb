<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Execution
 */
class Execution
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $definition;

    /**
     * @var integer
     */
    private $parent;

    /**
     * @var \DateTime
     */
    private $startedAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $variables;

    /**
     * @var string
     */
    private $waitingFor;

    /**
     * @var string
     */
    private $threads;

    /**
     * @var integer
     */
    private $nextThreadId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $states;
    
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $startedBy;

    /**
     * @var string
     */
    private $updatedBy;

    /**
     * @var \DateTime
     */
    private $canceledAt;

    /**
     * @var string
     */
    private $canceledBy;

    /**
     * @var \DateTime
     */
    private $endAt;

    /**
     * @var string
     */
    private $endBy;

    /**
     * @var \DateTime
     */
    private $suspendedAt;

    /**
     * @var boolean
     */
    private $cancellable;

    /**
     * @var string
     */
    private $suspendedStep;

    /**
     * @var array
     */
    private $roles;

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
     * Set definition
     *
     * @param integer $definition
     * @return Execution
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
     * Set parent
     *
     * @param integer $parent
     * @return Execution
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return integer 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     * @return Execution
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime 
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Execution
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set variables
     *
     * @param string $variables
     * @return Execution
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Get variables
     *
     * @return string 
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Set waitingFor
     *
     * @param string $waitingFor
     * @return Execution
     */
    public function setWaitingFor($waitingFor)
    {
        $this->waitingFor = $waitingFor;

        return $this;
    }

    /**
     * Get waitingFor
     *
     * @return string 
     */
    public function getWaitingFor()
    {
        return $this->waitingFor;
    }

    /**
     * Set threads
     *
     * @param string $threads
     * @return Execution
     */
    public function setThreads($threads)
    {
        $this->threads = $threads;

        return $this;
    }

    /**
     * Get threads
     *
     * @return string 
     */
    public function getThreads()
    {
        return $this->threads;
    }

    /**
     * Set nextThreadId
     *
     * @param integer $nextThreadId
     * @return Execution
     */
    public function setNextThreadId($nextThreadId)
    {
        $this->nextThreadId = $nextThreadId;

        return $this;
    }

    /**
     * Get nextThreadId
     *
     * @return integer 
     */
    public function getNextThreadId()
    {
        return $this->nextThreadId;
    }

    /**
     * Add states
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\ExecutionState $states
     * @return Execution
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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setStartedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Execution
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

    /**
     * Set startedBy
     *
     * @param string $startedBy
     * @return Execution
     */
    public function setStartedBy($startedBy)
    {
        $this->startedBy = $startedBy;

        return $this;
    }

    /**
     * Get startedBy
     *
     * @return string 
     */
    public function getStartedBy()
    {
        return $this->startedBy;
    }

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return Execution
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set canceledAt
     *
     * @param \DateTime $canceledAt
     * @return Execution
     */
    public function setCanceledAt($canceledAt)
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    /**
     * Get canceledAt
     *
     * @return \DateTime 
     */
    public function getCanceledAt()
    {
        return $this->canceledAt;
    }

    /**
     * Set canceledBy
     *
     * @param string $canceledBy
     * @return Execution
     */
    public function setCanceledBy($canceledBy)
    {
        $this->canceledBy = $canceledBy;

        return $this;
    }

    /**
     * Get canceledBy
     *
     * @return string 
     */
    public function getCanceledBy()
    {
        return $this->canceledBy;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Execution
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set endBy
     *
     * @param string $endBy
     * @return Execution
     */
    public function setEndBy($endBy)
    {
        $this->endBy = $endBy;

        return $this;
    }

    /**
     * Get endBy
     *
     * @return string 
     */
    public function getEndBy()
    {
        return $this->endBy;
    }

    /**
     * Set suspendedAt
     *
     * @param \DateTime $suspendedAt
     * @return Execution
     */
    public function setSuspendedAt($suspendedAt)
    {
        $this->suspendedAt = $suspendedAt;

        return $this;
    }

    /**
     * Get suspendedAt
     *
     * @return \DateTime 
     */
    public function getSuspendedAt()
    {
        return $this->suspendedAt;
    }


    /**
     * Set cancellable
     *
     * @param boolean $cancellable
     * @return Execution
     */
    public function setCancellable($cancellable)
    {
        $this->cancellable = $cancellable;

        return $this;
    }

    /**
     * Get cancellable
     *
     * @return boolean 
     */
    public function getCancellable()
    {
        return $this->cancellable;
    }

    /**
     * Set suspendedStep
     *
     * @param string $suspendedStep
     * @return Execution
     */
    public function setSuspendedStep($suspendedStep)
    {
        $this->suspendedStep = $suspendedStep;

        return $this;
    }

    /**
     * Get suspendedStep
     *
     * @return string 
     */
    public function getSuspendedStep()
    {
        return $this->suspendedStep;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return Execution
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * return true if username si in roles
     * @param string $username
     * @return boolean
     */
    public function hasRoleUsername($username)
    {
        if (null === $this->roles || 0 === count($this->roles)) {
            return false;
        }
        foreach ($this->roles as $role) {
            if ($role->getUsername() === $username) {
                return true;
            }
        }
        return false;
    }
}
