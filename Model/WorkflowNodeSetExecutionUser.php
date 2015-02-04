<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Twig_Environment;

/**
 * WorkflowNodeSetExecutionUser class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeSetExecutionUser extends WorkflowNode
{
    protected $configuration = array(
        'form_internal_name'=>null,
        'field_internal_name'=>null);

    /**
     * @param array $configuration
     */
	public function __construct( array $configuration )
    {
        if (!isset($configuration['form_internal_name'])) {
            $configuration['form_internal_name'] = null;
        }
        if (!isset($configuration['field_internal_name'])) {
            $configuration['field_internal_name'] = null;
        }
        parent::__construct( $configuration );
    }

    /**
     * @param WorkflowExecution $execution
     * @return array|string
     */
    protected function getRolesFromForm(WorkflowExecution $execution)
    {
        $result = $execution->getVariable($this->configuration['form_internal_name']);

        return $result[0][$this->configuration['field_internal_name']];
    }

    /**
     * @param WorkflowExecution $execution
     * @return boolean
     */
    public function execute( WorkflowExecution $execution )
    {

        if ( null === $this->configuration['form_internal_name'] ){
            throw new \Exception("Unable to use this node if form internal name is not set");
        }

        if ( null === $this->configuration['field_internal_name'] ){
            throw new \Exception("Unable to use this node if field internal name is not set");
        }

        $roles = $this->getRolesFromForm($execution);
        if (is_string($roles)) {
            $roles = array($roles);
        } 

        $execution->setRoles($roles);

		$this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );

    }

    public function verify() {
        parent::verify();

        if (null === $this->configuration['form_internal_name']) {
            throw new WorkflowInvalidWorkflowException('Node set execution user have not form internal name.');
        }

        if (null === $this->configuration['field_internal_name']) {
            throw new WorkflowInvalidWorkflowException('Node set execution user have not field internal name.');
        }
        
    }

} // END class WorkflowNodeEmail 