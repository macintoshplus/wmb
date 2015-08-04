<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;

/**
 * WorkflowNodeExternalCounter class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeExternalCounter extends WorkflowNode
{

    protected $configuration = array(
        'var_name'=>null,
        'counter_name'=>null
        );

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration = null)
    {

        if (!isset($configuration['var_name'])) {
            $configuration['var_name'] = null;
        }
        if (!isset($configuration['counter_name'])) {
            $configuration['counter_name'] = null;
        }

        parent::__construct($configuration);
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
        $configuration = array(
          'var_name'     => $element->getAttribute('var_name'),
          'counter_name' => $element->getAttribute('counter_name')
        );

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
        $element->setAttribute('var_name', $this->configuration['var_name']);
        $element->setAttribute('counter_name', $this->configuration['counter_name']);

    }

    /**
     * @return string
     */
    public function getVarName()
    {
        return $this->configuration['var_name'];
    }

    /**
     * @param string $varName
     * @return WorkflowNodeExternalCounter
     */
    public function setVarName($varName)
    {
        if (!is_string($varName)) {
            throw new BaseValueException('var_name', $varName, 'WorkflowNodeExternalCounter');
        }
        $this->configuration['var_name'] = $varName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCounterName()
    {
        return $this->configuration['counter_name'];
    }

    /**
     * @param string $counterName
     * @return WorkflowNodeExternalCounter
     */
    public function setCounterName($counterName)
    {
        if (!is_string($counterName)) {
            throw new BaseValueException('counter_name', $counterName, 'WorkflowNodeExternalCounter');
        }
        $this->configuration['counter_name'] = $counterName;

        return $this;
    }

    public function execute(WorkflowExecution $execution)
    {
        if (!$execution->hasCounter()) {
            $err = "Unable to use this node if counter service is not set";
            $execution->critical($err);
            throw new WorkflowExecutionException($err);
        }

        $val = $execution->getNext($this->configuration['counter_name']);

        $execution->setVariable($this->configuration['var_name'], $val);

        $execution->info(sprintf('Variable "%s" defined whith value "%s"', $this->configuration['var_name'], $val));

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);

    }

    public function verify()
    {
        parent::verify();

        if (null === $this->getVarName()) {
            throw new WorkflowInvalidWorkflowException('Node external counter has no variable name.');
        }

        if (null === $this->getCounterName()) {
            throw new WorkflowInvalidWorkflowException('Node external counter name has not set.');
        }

    }
}
