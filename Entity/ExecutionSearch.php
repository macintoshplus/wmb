<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;


/**
 * ExecutionSearch
 */
class ExecutionSearch
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
     * Set id
     *
     * @param integer $definition
     * @return ExecutionSearch
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * Set name
     *
     * @param string $name
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
     * @return ExecutionSearch
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
}
