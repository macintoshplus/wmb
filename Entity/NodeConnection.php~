<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NodeConnection
 */
class NodeConnection
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node
     */
    private $incomingNode;

    /**
     * @var \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node
     */
    private $outgoingNode;


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
     * Set incomingNode
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $incomingNode
     * @return NodeConnection
     */
    public function setIncomingNode(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $incomingNode = null)
    {
        $this->incomingNode = $incomingNode;

        return $this;
    }

    /**
     * Get incomingNode
     *
     * @return \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node 
     */
    public function getIncomingNode()
    {
        return $this->incomingNode;
    }

    /**
     * Set outgoingNode
     *
     * @param \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $outgoingNode
     * @return NodeConnection
     */
    public function setOutgoingNode(\JbNahan\Bundle\WorkflowManagerBundle\Entity\Node $outgoingNode = null)
    {
        $this->outgoingNode = $outgoingNode;

        return $this;
    }

    /**
     * Get outgoingNode
     *
     * @return \JbNahan\Bundle\WorkflowManagerBundle\Entity\Node 
     */
    public function getOutgoingNode()
    {
        return $this->outgoingNode;
    }
}
