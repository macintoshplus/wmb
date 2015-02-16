<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidInputException;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsAnything;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionInterface;

/**
 * An object of the WorkflowNodeInput class represents an input (from the application) node.
 *
 * When the node is reached, the workflow engine will suspend the workflow execution if the
 * specified input data is not available (first activation). While the workflow is suspended,
 * the application that embeds the workflow engine may supply the input data and resume the workflow
 * execution (second activation of the input node). Input data is stored in a workflow variable.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * This example creates a simple workflow that expectes two input variables,
 * once which can be any value and another that can only be an integer between
 * one and ten.
 *
 * <code>
 * <?php
 * $workflow = new Workflow('Test');
 *
 * $input = new WorkflowNodeInput(
 *   'mixedVar' => new WorkflowConditionIsAnything,
 *   'intVar'   => new WorkflowConditionAnd(
 *     array(
 *       new WorkflowConditionIsInteger,
 *       new WorkflowConditionIsGreatherThan(0)
 *       new WorkflowConditionIsLessThan(11)
 *     )
 *   )
 * );
 *
 * $input->addOutNode($workflow->endNode);
 * $workflow->startNode->addOutNode($input);
 * ?>
 * </code>
 *
 */
class WorkflowNodeInput extends WorkflowNode
{
    /**
     * Constructs a new input node.
     *
     * An input node accepts an array of workflow variables to accept
     * and/or together with a condition on the variable if required.
     *
     * Each element in the configuration array must be either
     * <b>String:</b> The name of the workflow variable to require. No conditions.
     *
     * or
     * <ul>
     *   <li><i>Key:</i> The name of the workflow variable to require.</li>
     *   <li><i>Value:</i> An object of type WorkflowConditionInterface</li>
     *
     * </ul>
     *
     * @param mixed $configuration
     * @throws BaseValueException
     */
    public function __construct($configuration = '')
    {
        if (!is_array($configuration)) {
            throw new BaseValueException(
              'configuration', $configuration, 'array'
            );
        }

        $tmp = array();

        foreach ($configuration as $key => $value) {
            if (is_int($key)) {
                if (!is_string($value)) {
                    throw new BaseValueException(
                      'workflow variable name', $value, 'string'
                    );
                }

                $variable  = $value;
                $condition = new WorkflowConditionIsAnything;
            } else {
                if (!is_object($value) || !$value instanceof WorkflowConditionInterface) {
                    throw new BaseValueException(
                      'workflow variable condition', $value, 'WorkflowConditionInterface'
                    );
                }

                $variable  = $key;
                $condition = $value;
            }

            $tmp[$variable] = $condition;
        }

        parent::__construct($tmp);
    }

    /**
     * Executes this node.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute(WorkflowExecution $execution)
    {
        $variables  = $execution->getVariables();
        $canExecute = true;
        $errors     = array();

        foreach ($this->configuration as $variable => $condition) {
            if (!isset($variables[$variable])) {
                $execution->addWaitingFor($this, $variable, $condition);

                $canExecute = false;
            } elseif (!$condition->evaluate($variables[$variable])) {
                $errors[$variable] = (string)$condition;
            }
        }

        if (!empty($errors)) {
            throw new WorkflowInvalidInputException($errors);
        }

        if ($canExecute) {
            $this->activateNode($execution, $this->outNodes[0]);

            return parent::execute($execution);
        } else {
            return false;
        }
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML(\DOMElement $element)
    {
        $configuration = array();

        foreach ($element->getElementsByTagName('variable') as $variable) {
            $configuration[$variable->getAttribute('name')] = WorkflowDefinitionStorageInterfaceXml::xmlToCondition(
              WorkflowDefinitionStorageInterfaceXml::getChildNode($variable)
            );
        }

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML(\DOMElement $element)
    {
        foreach ($this->configuration as $variable => $condition) {
            $xmlVariable = $element->appendChild(
              $element->ownerDocument->createElement('variable')
            );

            $xmlVariable->setAttribute('name', $variable);

            $xmlVariable->appendChild(
              WorkflowDefinitionStorageInterfaceXml::conditionToXml(
                $condition, $element->ownerDocument
              )
            );
        }
    }
}
