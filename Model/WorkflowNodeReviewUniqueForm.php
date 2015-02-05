<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsAnything;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsBool;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionVariableArrayLength;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual;

/**
 * WorkflowNodeReviewUniqueForm class
 * Review form : attention uniquement sur les formulaires devant avoir 1 réponses
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeReviewUniqueForm extends WorkflowNodeConditionalBranch
{
	protected $configuration = array(
	  'internal_name'=>null,
	  'roles'=>null,
      'condition' => array(),
      'else' => array()
    );
	
	protected $minInNodes = 1;

	protected $startNewThreadForBranch = false;


	public function __construct(array $configuration)
	{
		if (!isset($configuration['internal_name'])) {
			$configuration['internal_name'] = null;
		}
		if (!isset($configuration['roles'])) {
			$configuration['roles'] = null;
		}
		if (!isset($configuration['condition'])) {
			$configuration['condition'] = array();
		}
		if (!isset($configuration['else'])) {
			$configuration['else'] = array();
		}
        parent::__construct( $configuration );
	}

	/**
	 * return internal name
	 * this name is use when ID for Type Form link
	 * @return string
	 */
	public function getInternalName()
	{
		return $this->configuration['internal_name'];
	}

	/**
	 * @param string $internalName
	 * @return WorkflowNodeReviewUniqueForm
	 */
	public function setInternalName($internalName)
	{
		$this->configuration['internal_name'] = $internalName;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getReviewData(WorkflowExecution $execution) {
		return $execution->getVariable($this->getInternalName() . '_review');
	}

	/**
	 * @return array
	 */
	public function getFormData(WorkflowExecution $execution) {
		$value = $execution->getVariable($this->getInternalName());
		return $value[0];
	}
	
	/**
	 * @return null|array
	 */
	public function getRoles()
	{
		return $this->configuration['roles'];
	}

	/**
	 * @param array $roles
	 * @return WorkflowNodeReviewUniqueForm
	 */
	public function setRoles(array $roles = null)
	{
		$this->configuration['roles'] = $roles;

		return $this;
	}

	/**
	 * @param WorkflowNode $outNode
	 * @param WorkflowNode $else
	 * @return Workflow
	 */
	public function addSelectOutNode( WorkflowNode $outNode, WorkflowNode $else )
    {
    	$equal = new WorkflowConditionIsEqual(0);
    	$condition = new WorkflowConditionVariableArrayLength($this->getInternalName() . '_review', $equal);
    	return parent::addConditionalOutNode($condition, $outNode, $else);
    }

	public function execute( WorkflowExecution $execution )
    {
    	$canExecute = true;
        $formName = $this->getInternalName();
        $formNameResponse = $formName.'_response';
        $formNameContinue = $formName.'_continue';
        $formNameReview = $formName.'_review';
        //$variables = $execution->getVariables();
        
        //Vérifie que les données sont renseignées
        if (!$execution->hasVariable($formNameReview)) {
        	return false;
        }

        return parent::execute( $execution );

    }
	public function verify() {
    	parent::verify();

    	//pas le nom interne du formulaire à review
    	if (null === $this->getInternalName()) {
    		throw new WorkflowInvalidWorkflowException('Node Review unique form has no form internal name.');
    	}
    	
    }
}
