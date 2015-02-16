<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionNot;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;

/**
 * Abstract base class for nodes that conditionally branch multiple threads of
 * execution.
 *
 * Most implementations only need to set the conditions for proper functioning.
 *
 */
abstract class WorkflowNodeConditionalBranch extends WorkflowNodeBranch
{
    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to have. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minConditionalOutNodes = false;

    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minActivatedConditionalOutNodes = false;

    /**
     * Constraint: The maximum number of conditional outgoing nodes this node
     * may activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxActivatedConditionalOutNodes = false;

    /**
     * Holds the conditions of the out nodes.
     *
     * The key is the position of the out node in the array of out nodes.
     *
     * @var array('condition' => array('int' => WorkflowCondtion))
     */
    protected $configuration = array(
      'condition' => array(),
      'else' => array()
    );

    /**
     * Adds the conditional outgoing node $outNode to this node with the
     * condition $condition. Optionally, an $else node can be specified that is
     * activated when the $condition evaluates to false.
     *
     * @param WorkflowConditionInterface $condition
     * @param WorkflowNode      $outNode
     * @param WorkflowNode      $else
     * @return WorkflowNode
     */
    public function addConditionalOutNode(WorkflowConditionInterface $condition, WorkflowNode $outNode, WorkflowNode $else = null)
    {
        $this->addOutNode($outNode);
        $this->configuration['condition'][array_search($outNode, $this->outNodes, true)] = $condition;

        if (!is_null($else)) {
            $this->addOutNode($else);

            $key = array_search($else, $this->outNodes, true );
            $this->configuration['condition'][$key] = new WorkflowConditionNot($condition);
            $this->configuration['else'][$key] = true;
        }

        return $this;
    }

    /**
     * Returns the condition for a conditional outgoing node
     * and false if the passed not is not a (unconditional)
     * outgoing node of this node.
     *
     * @param  WorkflowNode $node
     * @return WorkflowConditionInterface
     * @ignore
     */
    public function getCondition(WorkflowNode $node)
    {
        $keys    = array_keys($this->outNodes);
        $numKeys = count($keys);

        for ($i = 0; $i < $numKeys; $i++) {
            if ($this->outNodes[$keys[$i]] === $node) {
                if (isset($this->configuration['condition'][$keys[$i]])) {
                    return $this->configuration['condition'][$keys[$i]];
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Returns true when the $node belongs to an ELSE condition.
     *
     * @param WorkflowNode $node
     * @return bool
     * @ignore
     */
    public function isElse(WorkflowNode $node)
    {
        return isset($this->configuration['else'][array_search($node, $this->outNodes, true)]);
    }

    /**
     * Evaluates all the conditions, checks the constraints and activates any nodes that have
     * passed through both checks and condition evaluation.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(WorkflowExecution $execution)
    {
        $keys                            = array_keys($this->outNodes);
        $numKeys                         = count($keys);
        $nodesToStart                    = array();
        $numActivatedConditionalOutNodes = 0;

        if ($this->maxActivatedConditionalOutNodes !== false) {
            $maxActivatedConditionalOutNodes = $this->maxActivatedConditionalOutNodes;
        } else {
            $maxActivatedConditionalOutNodes = $numKeys;
        }

        for ($i = 0; $i < $numKeys && $numActivatedConditionalOutNodes <= $maxActivatedConditionalOutNodes; $i++) {
            if (isset($this->configuration['condition'][$keys[$i]])) {
                // Conditional outgoing node.
                if ($this->configuration['condition'][$keys[$i]]->evaluate($execution->getVariables())) {
                    $nodesToStart[] = $this->outNodes[$keys[$i]];
                    $numActivatedConditionalOutNodes++;
                }
            } else {
                // Unconditional outgoing node.
                $nodesToStart[] = $this->outNodes[$keys[$i]];
            }
        }

        if ($this->minActivatedConditionalOutNodes !== false && $numActivatedConditionalOutNodes < $this->minActivatedConditionalOutNodes) {
            throw new WorkflowExecutionException(
              'Node activates less conditional outgoing nodes than required.'
            );
        }

        return $this->activateOutgoingNodes($execution, $nodesToStart);
    }

    /**
     * Checks this node's constraints.
     *
     * @throws WorkflowInvalidWorkflowException if the constraints of this node are not met.
     */
    public function verify()
    {
        parent::verify();

        $numConditionalOutNodes = count($this->configuration['condition']);

        if ($this->minConditionalOutNodes !== false && $numConditionalOutNodes < $this->minConditionalOutNodes) {
            throw new WorkflowInvalidWorkflowException(
              'Node has less conditional outgoing nodes than required.'
            );
        }
    }
}
