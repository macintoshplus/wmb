<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsAnything;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsArray;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsBool;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionVariableArrayLength;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual;
use JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter\NodeVoterInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException;

/**
 * WorkflowNodeReviewUniqueForm class
 * Review form : attention uniquement sur les formulaires devant avoir 1 réponses
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeReviewUniqueForm extends WorkflowNodeConditionalBranch implements NodeVoterInterface
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
        parent::__construct($configuration);
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
    public function getReviewData(WorkflowExecution $execution)
    {
        return $execution->getVariable($this->getInternalName() . WorkflowNodeForm::PREFIX_REVIEW);
    }

    /**
     * @return array
     */
    public function getFormData(WorkflowExecution $execution)
    {
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
    public function addSelectOutNode(WorkflowNode $outNode, WorkflowNode $else)
    {
        $equal = new WorkflowConditionIsEqual(0);
        $condition = new WorkflowConditionVariableArrayLength($this->getInternalName() . '_review', $equal);
        return parent::addConditionalOutNode($condition, $outNode, $else);
    }

    /**
     * return true if username si in roles
     * @param string $username
     * @return boolean
     */
    public function hasRoleUsername($username)
    {
        if (null === $this->getRoles() || 0 === count($this->getRoles())) {
            return false;
        }
        foreach ($this->getRoles() as $role) {
            if ($role === $username) {
                return true;
            }
        }
        return false;
    }

    public function hasRoles(array $roles)
    {
        foreach ($roles as $role) {
            if (true === $this->hasRoleUsername($role)) {
                return true;
            }
        }
        return false;
    }

    public function execute(WorkflowExecution $execution)
    {
        $canExecute = true;
        $formName = $this->getInternalName();
        $formNameResponse = $formName.WorkflowNodeForm::PREFIX_RESPONSE; //'_response';
        $formNameContinue = $formName.WorkflowNodeForm::PREFIX_CONTINUE; //'_continue';
        $formNameReview = $formName.WorkflowNodeForm::PREFIX_REVIEW; //'_review';
        //$variables = $execution->getVariables();
        
        //Vérifie que les données sont renseignées
        if (!$execution->hasVariable($formNameReview)) {
            $execution->addWaitingFor($this, $formNameReview, new WorkflowConditionIsArray());
            return false;
        }

        return parent::execute($execution);

    }
    public function verify()
    {
        parent::verify();

        //pas le nom interne du formulaire à review
        if (null === $this->getInternalName()) {
            throw new WorkflowInvalidWorkflowException('Node Review unique form has no form internal name.');
        }
        
    }
}
