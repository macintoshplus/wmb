<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;

/**
 * DefinitionSearch
 */
class DefinitionSearch
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
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
     * Set createdBy
     *
     * @param string $createdBy
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
     */
    public function setRolesForUpdate($rolesForUpdate)
    {
        $this->rolesForUpdate = $rolesForUpdate;

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
     * @return Workflow
     */
    public function setRolesForUse($rolesForUse)
    {
        $this->rolesForUse = $rolesForUse;

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
