<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

interface WorkflowNodeFormResponseInterface
{

    /**
     * Get id
     *
     * @param integer $id
     * @return WorkflowNodeFormResponseInterface
     */
    public function setId($id);

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();
    
    /**
     * Set answeredBy
     *
     * @param string $answeredBy
     * @return WorkflowNodeFormResponseInterface
     */
    public function setAnsweredBy($answeredBy);

    /**
     * Get answeredBy
     *
     * @return string
     */
    public function getAnsweredBy();

    /**
     * Set answers
     *
     * @param array $answers
     * @return WorkflowNodeFormResponseInterface
     */
    public function setAnswers($answers);

    /**
     * Get answers
     *
     * @return array
     */
    public function getAnswers();

    /**
     * Set answeredAt
     *
     * @param \DateTime $answeredAt
     * @return WorkflowNodeFormResponseInterface
     */
    public function setAnsweredAt($answeredAt);

    /**
     * Get answeredAt
     *
     * @return \DateTime
     */
    public function getAnsweredAt();

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return WorkflowNodeFormResponseInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return WorkflowNodeFormResponseInterface
     */
    public function setUpdatedBy($updatedBy);

    /**
     * Get updatedBy
     *
     * @return string 
     */
    public function getUpdatedBy();


    /**
     * Set name
     *
     * @param string $name
     * @return WorkflowNodeFormResponseInterface
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string 
     */
    public function getName();


    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return WorkflowNodeFormResponseInterface
     */
    public function setDeletedAt($deletedAt);

    /**
     * Get deletedAt
     *
     * @return \DateTime 
     */
    public function getDeletedAt();

    /**
     * Set deletedBy
     *
     * @param string $deletedBy
     * @return WorkflowNodeFormResponseInterface
     */
    public function setDeletedBy($deletedBy);

    /**
     * Get deletedBy
     *
     * @return string 
     */
    public function getDeletedBy();


    /**
     * return boolean if a key answers is set
     * 
     * @see getOption()
     * @see setOption()
     * @see clearOption()
     * 
     * @param string $key
     * @return boolean
     */
    public function hasAnswer($key);

    /**
     * Null si la clé n'existe pas.
     * 
     * @param string $key
     * @return null|mixed
     */
    public function getAnswer($key);

    /**
     * Null si la clé n'existe pas.
     * 
     * @param string $key
     * @param mixed  $value
     * @return FormElement
     */
    public function setAnswer($key, $value);

    /**
     * Null si la clé n'existe pas.
     * 
     * @param string $key
     * @return null|boolean
     */
    public function clearAnswer($key);
}
