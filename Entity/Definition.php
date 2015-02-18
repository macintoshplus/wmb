<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Definition
 */
class Definition
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $version;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $nodes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $executions;

    /**
     * @var string
     */
    private $createdBy;

    /**
     * @var string
     */
    private $updatedBy;

    /**
     * @var integer
     */
    private $parent;

    /**
     * @var \DateTime
     */
    private $publishedAt;

    /**
     * @var string
     */
    private $publishedBy;

    /**
     * @var \DateTime
     */
    private $archivedAt;

    /**
     * @var string
     */
    private $archivedBy;

    /**
     * @var array
     */
    private $rolesForUpdate;

    /**
     * @var array
     */
    private $rolesForUse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nodes = new \Doctrine\Common\Collections\ArrayCollection();
        //$this->executions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get id
     *
     * @return integer 
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Definition
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
     * Set version
     *
     * @param integer $version
     * @return Definition
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Definition
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Definition
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
     * Add nodes
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $nodes
     * @return Definition
     */
    public function addNode(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $nodes)
    {
        $this->nodes[] = $nodes;

        return $this;
    }

    /**
     * Remove nodes
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $nodes
     */
    public function removeNode(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $nodes)
    {
        $this->nodes->removeElement($nodes);
    }

    /**
     * Get nodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Add executions
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution $executions
     * @return Definition
     */
    // public function addExecution(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution $executions)
    // {
    //     $this->executions[] = $executions;

    //     return $this;
    // }

    /**
     * Remove executions
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution $executions
     */
    // public function removeExecution(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution $executions)
    // {
    //     $this->executions->removeElement($executions);
    // }

    /**
     * Get executions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    // public function getExecutions()
    // {
    //     return $this->executions;
    // }
    
    
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
    
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return Definition
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return Definition
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
     * Set parent
     *
     * @param integer $parent
     * @return Definition
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
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return Definition
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set publishedBy
     *
     * @param string $publishedBy
     * @return Definition
     */
    public function setPublishedBy($publishedBy)
    {
        $this->publishedBy = $publishedBy;

        return $this;
    }

    /**
     * Get publishedBy
     *
     * @return string 
     */
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }

    /**
     * Set archivedAt
     *
     * @param \DateTime $archivedAt
     * @return Definition
     */
    public function setArchivedAt($archivedAt)
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    /**
     * Get archivedAt
     *
     * @return \DateTime 
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * Set archivedBy
     *
     * @param string $archivedBy
     * @return Definition
     */
    public function setArchivedBy($archivedBy)
    {
        $this->archivedBy = $archivedBy;

        return $this;
    }

    /**
     * Get archivedBy
     *
     * @return string 
     */
    public function getArchivedBy()
    {
        return $this->archivedBy;
    }

    /**
     * Set rolesForUpdate
     *
     * @param array $rolesForUpdate
     * @return Definition
     */
    public function setRolesForUpdate($rolesForUpdate)
    {
        $filter = array_filter($rolesForUpdate);
        if (0 === count($filter)) {
            $filter = null;
        }
        $this->rolesForUpdate = $filter;


        return $this;
    }

    /**
     * Get rolesForUpdate
     *
     * @return array 
     */
    public function getRolesForUpdate()
    {
        return $this->rolesForUpdate;
    }

    /**
     * Set rolesForUse
     *
     * @param array $rolesForUse
     * @return Definition
     */
    public function setRolesForUse($rolesForUse)
    {
        if (is_array($rolesForUse)) {
            $filter = array_filter($rolesForUse);
            if (0 === count($filter)) {
                $filter = null;
            }
            $this->rolesForUse = $filter;
        } else {
            $this->rolesForUse = null;
        }

        return $this;
    }

    /**
     * Get rolesForUse
     *
     * @return array 
     */
    public function getRolesForUse()
    {
        return $this->rolesForUse;
    }
}
