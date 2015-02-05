<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;

/**
 * WorkflowNodeExternalCounter class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeExternalCounter extends WorkflowNode
{
	/**
	 * @var WorkflowExternalCounterInterface
	 */
	protected $counter;

	protected $configuration = array(
		'var_name'=>null,
		'counter_name'=>null
		);

	/**
     * @param array $configuration
     */
	public function __construct( array $configuration = null )
    {

        if ( !isset( $configuration['var_name'] ) )
        {
            $configuration['var_name'] = 'counter';
        }
        
        parent::__construct( $configuration );
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
        $this->configuration['var_name'] = $var_name;

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

	/**
	 * @param WorkflowExternalCounterInterface $counter
	 * @return WorkflowNodeExternalCounter
	 */
	public function setExternalCounter(WorkflowExternalCounterInterface $counter)
	{
		$this->counter = $counter;

		return $this;
	}

	public function execute( WorkflowExecution $execution )
    {
    	if ( !isset( $this->counter ) ){
        	throw new \Exception("Unable to use this node if counter service is not set");
        }

        $execution->setVariable($this->configuration['var_name'], $this->counter->getNext($this->configuration['counter_name']));

		$this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );

    }

    public function verify() {
    	parent::verify();

    	if (null === $this->configuration['var_name']) {
    		throw new WorkflowInvalidWorkflowException('Node external counter has no variable name.');
    	}

    	if (null === $this->configuration['counter_name']) {
    		throw new WorkflowInvalidWorkflowException('Node external counter name has not set.');
    	}
    	
    }
}