<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitor;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowExecution;

abstract class WorkflowNode implements WorkflowVisitableInterface
{
    /**
     * The node is waiting to be activated.
     */
    const WAITING_FOR_ACTIVATION = 0;

    /**
     * The node is activated and waiting to be executed.
     */
    const WAITING_FOR_EXECUTION = 1;

    /**
     * Unique ID of this node.
     *
     * Only available when the workflow this node belongs to has been loaded
     * from or saved to the data storage.
     *
     * @var integer
     */
    protected $id = false;

    /**
     * The incoming nodes of this node.
     *
     * @var array(int => WorkflowNode)
     */
    protected $inNodes = array();

    /**
     * The outgoing nodes of this node.
     *
     * @var array(int => WorkflowNode)
     */
    protected $outNodes = array();

    /**
     * Constraint: The minimum number of incoming nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minInNodes = 1;

    /**
     * Constraint: The maximum number of incoming nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxInNodes = 1;

    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minOutNodes = 1;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxOutNodes = 1;

    /**
     * The number of incoming nodes.
     *
     * @var integer
     */
    protected $numInNodes = 0;

    /**
     * The number of outgoing nodes.
     *
     * @var integer
     */
    protected $numOutNodes = 0;

    /**
     * The configuration of this node.
     *
     * The configuration is a structured (hash) array with the
     * various options of the implemented node.
     *
     * This functionality is implemented as an array to make it possible
     * to have the storage engines unaware of the node classes.
     *
     * @var array(config key => config value)
     */
    protected $configuration;

    /**
     * The state of this node.
     *
     * @var integer
     */
    protected $activationState;

    /**
     * The node(s) that activated this node.
     *
     * @var WorkflowNode[]
     */
    protected $activatedFrom = array();

    /**
     * The state of this node.
     *
     * @var mixed
     */
    protected $state = null;

    /**
     * The id of the thread this node is executing in.
     *
     * @var integer
     */
    protected $threadId = null;

    /**
     * Flag that indicates whether an add*Node() or remove*Node()
     * call is internal. This is necessary to avoid unlimited loops. 
     *
     * @var boolean
     */
    protected static $internalCall = false;

    protected $name = null;

    /**
     * Constructs a new node with the configuration $configuration.
     *
     * The configuration is a structured (hash) array. Implementations
     * must pass their complete configuration on to this object. We have
     * chosen to use structured arrays for the configuration since it
     * simplifies the process of creating new node types and storing workflows.
     *
     * @param mixed $configuration
     */
    public function __construct($configuration = null)
    {
        if ($configuration !== null) {
            $this->configuration = $configuration;
        }

        $this->initState();
    }

    /**
     * Adds a node to the incoming nodes of this node.
     *
     * Automatically adds $node to the workflow and adds
     * this node as an out node of $node.
     *
     * @param  WorkflowNode $node The node that is to be added as incoming node.
     * @throws WorkflowInvalidWorkflowException if the operation violates the constraints of the nodes involved.
     * @return WorkflowNode
     */
    public function addInNode(WorkflowNode $node)
    {
        // Check whether the node is already an incoming node of this node.
        if (in_array($node, $this->inNodes) === false) {
            // Add this node as an outgoing node to the other node.
            if (!self::$internalCall) {
                self::$internalCall = true;
                $node->addOutNode($this);
            } else {
                self::$internalCall = false;
            }

            // Add the other node as an incoming node to this node.
            $this->inNodes[] = $node;
            $this->numInNodes++;
        }

        return $this;
    }

    /**
     * Removes a node from the incoming nodes of this node.
     *
     * Automatically removes $this as an out node of $node.
     *
     * @param  WorkflowNode $node The node that is to be removed as incoming node.
     * @throws WorkflowInvalidWorkflowException if the operation violates the constraints of the nodes involved.
     * @return boolean
     */
    public function removeInNode(WorkflowNode $node)
    {
        $index = array_search($node, $this->inNodes, true);

        if ($index !== false) {
            // Remove this node as an outgoing node from the other node.
            if (!self::$internalCall) {
                self::$internalCall = true;
                $node->removeOutNode($this);
            } else {
                self::$internalCall = false;
            }

            unset($this->inNodes[$index]);
            $this->numInNodes--;

            return true;
        }

        return false;
    }

    public function removeAllInNode()
    {
        foreach ($this->inNodes as $node) {
            $result = $this->removeInNode($node);
            if (false === $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * Adds a node to the outgoing nodes of this node.
     *
     * Automatically adds $node to the workflow and adds
     * this node as an in node of $node.
     *
     * @param  WorkflowNode $node The node that is to be added as outgoing node.
     * @throws WorkflowInvalidWorkflowException if the operation violates the constraints of the nodes involved.
     * @return WorkflowNode
     */
    public function addOutNode(WorkflowNode $node)
    {
        // Check whether the other node is already an outgoing node of this node.
        if (in_array($node, $this->outNodes) === false) {
            // Add this node as an incoming node to the other node.
            if (!self::$internalCall) {
                self::$internalCall = true;
                $node->addInNode($this);
            } else {
                self::$internalCall = false;
            }

            // Add the other node as an outgoing node to this node.
            $this->outNodes[] = $node;
            $this->numOutNodes++;
        }

        return $this;
    }

    /**
     * Removes a node from the outgoing nodes of this node.
     *
     * Automatically removes $this as an in node of $node.
     *
     * @param  WorkflowNode $node The node that is to be removed as outgoing node.
     * @throws WorkflowInvalidWorkflowException if the operation violates the constraints of the nodes involved.
     * @return boolean
     */
    public function removeOutNode(WorkflowNode $node)
    {
        $index = array_search($node, $this->outNodes, true);

        if ($index !== false) {
            // Remove this node as an incoming node from the other node.
            if (!self::$internalCall) {
                self::$internalCall = true;
                $node->removeInNode($this);
            } else {
                self::$internalCall = false;
            }

            unset($this->outNodes[$index]);
            $this->numOutNodes--;

            return true;
        }

        return false;
    }

    public function removeAllOutNode()
    {
        foreach ($this->outNodes as $node) {
            $result = $this->removeOutNode($node);
            if (false === $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the Id of this node.
     *
     * @return integer
     * @ignore
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Id of this node.
     *
     * @param int $id
     * @ignore
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name=$name;
    }
    /**
     * Sets the activation state for this node.
     *
     * One of WorkflowNode::WAITING_FOR_ACTIVATION or
     * WorkflowNode::WAITING_FOR_EXECUTION.
     *
     * @param int $activationState
     * @ignore
     */
    public function setActivationState($activationState)
    {
        if ($activationState == self::WAITING_FOR_ACTIVATION || $activationState == self::WAITING_FOR_EXECUTION) {
            $this->activationState = $activationState;
        }
    }

    /**
     * Returns the incoming nodes of this node.
     *
     * @return WorkflowNode[]
     */
    public function getInNodes()
    {
        return $this->inNodes;
    }

    /**
     * Returns the outgoing nodes of this node.
     *
     * @return WorkflowNode[]
     */
    public function getOutNodes()
    {
        return $this->outNodes;
    }

    /**
     * Returns the configuration of this node.
     *
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the state of this node.
     *
     * @return mixed
     * @ignore
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the state of this node.
     *
     * @param mixed $state
     * @ignore
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Returns the node(s) that activated this node.
     *
     * @return array
     * @ignore
     */
    public function getActivatedFrom()
    {
        return $this->activatedFrom;
    }

    /**
     * Sets the node(s) that activated this node.
     *
     * @param array $activatedFrom
     * @ignore
     */
    public function setActivatedFrom(array $activatedFrom)
    {
        $this->activatedFrom = $activatedFrom;
    }

    /**
     * Returns the id of the thread this node is executing in.
     *
     * @return integer
     * @ignore
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * Sets the id of the thread this node is executing in.
     *
     * @param int $threadId
     * @ignore
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;
    }

    /**
     * Checks this node's constraints.
     *
     * The constraints checked are the minimum in nodes
     * maximum in nodes, minimum out nodes and maximum
     * out nodes.
     *
     * @throws WorkflowInvalidWorkflowException if the constraints of this node are not met.
     */
    public function verify()
    {
        $type = str_replace('WorkflowNode', '', get_class($this));

        if ($this->minInNodes !== false && $this->numInNodes < $this->minInNodes) {
            throw new WorkflowInvalidWorkflowException(
                sprintf(
                    'Node of type "%s" has less incoming nodes than required.',
                    $type
                )
            );
        }

        if ($this->maxInNodes !== false && $this->numInNodes > $this->maxInNodes) {
            throw new WorkflowInvalidWorkflowException(
                sprintf(
                    'Node of type "%s" has more incoming nodes than allowed.',
                    $type
                )
            );
        }

        if ($this->minOutNodes !== false && $this->numOutNodes < $this->minOutNodes) {
            throw new WorkflowInvalidWorkflowException(
                sprintf(
                    'Node of type "%s" has less outgoing nodes than required.',
                    $type
                )
            );
        }

        if ($this->maxOutNodes !== false && $this->numOutNodes > $this->maxOutNodes) {
            throw new WorkflowInvalidWorkflowException(
                sprintf(
                    'Node of type "%s" has more outgoing nodes than allowed.',
                    $type
                )
            );
        }
    }

    /**
     * Reimplementation of accept() calls accept on all out nodes.
     *
     * @param WorkflowVisitor $visitor
     */
    public function accept(WorkflowVisitor $visitor)
    {
        if ($visitor->visit($this)) {
            foreach ($this->outNodes as $outNode) {
                $outNode->accept($visitor);
            }
        }
    }

    /**
     * Activate this node in the execution environment $execution.
     *
     * $activatedFrom is the node that activated this node and $threadId is
     * threadId of the thread the node should be activated in.
     *
     * This method is called by other nodes and/or the execution environment
     * depending on the workflow.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode $activatedFrom
     * @param int $threadId
     * @ignore
     */
    public function activate(WorkflowExecution $execution, WorkflowNode $activatedFrom = null, $threadId = 0)
    {
        if ($this->activationState === self::WAITING_FOR_ACTIVATION) {
            $this->activationState = self::WAITING_FOR_EXECUTION;
            $this->setThreadId($threadId);

            if ($activatedFrom !== null) {
                $this->activatedFrom[] = get_class($activatedFrom);
            }

            $execution->activate($this);
        }
    }

    /**
     * Convenience method for activating an (outgoing) node.
     *
     * @param WorkflowExecution $execution
     * @param WorkflowNode $node
     */
    protected function activateNode(WorkflowExecution $execution, WorkflowNode $node)
    {
        $node->activate($execution, $this, $this->getThreadId());
    }

    /**
     * Returns true if this node is ready for execution
     * and false if it is not.
     *
     * @return boolean
     * @ignore
     */
    public function isExecutable()
    {
        return $this->activationState === self::WAITING_FOR_EXECUTION;
    }

    /**
     * Executes and performs the workflow duties of this node
     * and returns true if the node completed execution.
     *
     * Implementations of WorkflowNode should reimplement this method.
     *
     * This method is called automatically by the workflow execution
     * environment and should not be called directly.
     *
     * The default implementation resets the activation state of the
     * node.
     *
     * @param  WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(WorkflowExecution $execution)
    {
        $this->activationState = self::WAITING_FOR_ACTIVATION;
        $this->activatedFrom = array();
        $this->threadId = null;

        return true;
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @ignore
     */
    public static function configurationFromXML(\DOMElement $element)
    {
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML(\DOMElement $element)
    {
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        //return get_class($this);
        $className = get_class($this);
        $elements = explode('\\', $className);
        $type = $elements[count($elements)-1];

        $type   = str_replace('WorkflowNode', '', $type);
        $max    = strlen($type);
        $string = '';

        for ($i = 0; $i < $max; $i++) {
            if ($i > 0 && ord($type[$i]) >= 65 && ord($type[$i]) <= 90) {
                $string .= ' ';
            }

            $string .= $type[$i];
        }
        
        if (null !== $this->name) {
            $string = sprintf('%s (%s)', $this->name, $string);
        }

        return $string;
        
    }

    /**
     * Initializes the state of this node.
     *
     * @ignore
     */
    public function initState()
    {
        $this->activatedFrom = array();
        $this->state         = null;
        $this->threadId      = null;

        $this->setActivationState(self::WAITING_FOR_ACTIVATION);
    }
}
