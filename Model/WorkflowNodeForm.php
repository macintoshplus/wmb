<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsAnything;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsBool;

/**
 * WorkflowNodeForm class
 * Multiple response for one form
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeForm extends WorkflowNode
{
	protected $configuration = array(
		'min_response'=>1,
		'max_response'=>false,
		'internal_name'=>null,
		'auto_continue'=>false,
		'roles'=>null
		);

	public function __construct(array $configuration)
	{
		if ( !isset( $configuration['min_response'] ) || !is_integer($configuration['min_response']) || 1 < $configuration['min_response'])
        {
        	$configuration['min_response'] = $this->configuration['min_response'];
        }

		if ( !isset( $configuration['max_response'] ) || (!is_integer($configuration['max_response']) && !is_bool($configuration['max_response'])) )
        {
        	$configuration['max_response'] = $this->configuration['max_response'];
        }

        if (false !== $configuration['max_response'] && $configuration['max_response'] < $configuration['min_response'])
        {
        	$configuration['max_response'] = $configuration['min_response'];
        }

		if ( !isset( $configuration['auto_continue'] ) || (!is_bool($configuration['auto_continue'])) )
        {
        	$configuration['auto_continue'] = $this->configuration['auto_continue'];
        }

        if ( !isset( $configuration['internal_name'] ) || null === $configuration['internal_name'] ) {
        	$configuration['internal_name'] = sprintf('form_%d', time());
        }

		if (!isset($configuration['roles'])) {
			$configuration['roles'] = null;
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
	 * Return true if auto continue is disabled
	 * @return boolean
	 */
	public function doConfirmContinue()
	{
		//Si plusieurs quelque soit auto_continue
		// ou auto_continue a faux
		return (false === $this->configuration['max_response'] || 1 < $this->configuration['max_response'] || !$this->configuration['auto_continue']);
	}

	/**
	 * @return integer
	 */
	public function getMinResponse()
	{
		return $this->configuration['min_response'];
	}
	/**
	 * @return false|integer
	 */
	public function getMaxResponse()
	{
		return $this->configuration['max_response'];
	}
	
	/**
	 * @return null|array
	 */
	public function getRoles()
	{
		return $this->configuration['roles'];
	}

	/**
	 * return true if the response count is valid
	 * @return boolean
	 */
	public function responseIsEnough(WorkflowExecution $execution)
	{
		$nbResponse = count($this->getResponses($execution));
		//réponse unique
		if ($this->getMinResponse() === $this->getMaxResponse() && 1 === $nbResponse) { return true; }
		//réponse multiple
		//nombre de réponse inférieur au minimum
		if ($this->getMinResponse() > $nbResponse) { return false; }
		//nb de réponse supérieur au minimum, inférieur ou égaleme au max ?
		//Cas du max = faux : répond toujour vrai
		if (false === $this->getMaxResponse()) { return true; }
		//Cas du max <= nb
		if ($this->getMaxResponse() >= $nbResponse) { return true; }
		//Par défaut
		return false;
	}

	public function getResponses(WorkflowExecution $execution)
	{
		return $execution->getVariable($this->configuration['internal_name']);
	}

    public function execute( WorkflowExecution $execution )
    {
    	$canExecute = true;
        $formName = $this->configuration['internal_name'];
        $formNameResponse = $formName.'_response';
        $formNameContinue = $formName.'_continue';
        $formNameReview = $formName.'_review';
        //$variables = $execution->getVariables();
        
        //Vérifie que les données sont renseignées
        if (!$execution->hasVariable($formName)) {
        	$execution->setVariable($formName, array());
        	$canExecute = false;
        }

        //Vérifie si une réponse a été fournie
		if ($execution->hasVariable($formNameResponse)) {
			//Vérifie si il y a une review, si oui, il supprime
			if ($execution->hasVariable($formNameReview)) {
				$execution->unsetVariable($formNameReview);
			}
			
			//Déplace les données
			$response = $execution->getVariable($formNameResponse);
			$responses = $this->getResponses($execution);
			//Si un ID est fourni, il l'extrait
			if (array_key_exists('id', $response)) {
				//récupère la clée
				$key = $response['id'];
				//supprime la clé des données (s'en est pas une)
				unset($response['id']);

				//si aucune données n'est présente pour cette clé, elle est effacé.
				if (!array_key_exists($key, $responses)) {
					unset($key);
				}
			}

			//si une réponse possible, il ajoute les données en remplaçant celle eventuellement présente
			if ($this->getMaxResponse() === $this->getMinResponse()) {
				$responses = array($response);
			} else {
				//Sinon, si la clée est initialisée, remplacement des données, sinon, ajout
				if (isset($key)) {
					$responses[$key] = $response;
				} else {
					if (false === $this->getMaxResponse() || count($responses) < $this->getMaxResponse()) {
						$responses[] = $response;
					}
				}
				
			}

        	$execution->setVariable($formName, $responses);
        	//supprime la réponses
        	$execution->unsetVariable($formNameResponse);
			
			//Si il n'y a un nombre suffisent de réponse, il faut passer automatiquement à la suite.
			if ( $this->responseIsEnough($execution) && $this->configuration['auto_continue']) {
				$canExecute = true;
			} else {
				$canExecute = false;
			}

		} else {
			//ne passe pas si aucune saisie
			$canExecute = false;
		}

		//Passe si la variable de passage est initialisé même sans données saisie.
		//Il faut qu'il y ai le nombre de réponse necessaire
		if ($execution->hasVariable($formNameContinue) && $this->responseIsEnough($execution)) {
        	$execution->unsetVariable($formNameContinue);
			$canExecute = true;
		}


        if (!$canExecute) {
        	//echo "Add variables\n";
        	//Ajoute la variable de réponse
        	$execution->addWaitingFor( $this, $formNameResponse, new WorkflowConditionIsAnything() );
        	//Si plusieurs ou auto réponse a faux
        	if ($this->doConfirmContinue()) {
        		$execution->addWaitingFor( $this, $formNameContinue, new WorkflowConditionIsBool() );
        	}
        	return false;
        } 

		$this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );

    }

    public function verify() {
    	parent::verify();

    	//une réponse mais pas 1
    	if ($this->getMinResponse() === $this->getMaxResponse() && $this->getMinResponse() !== 1) {
    		throw new WorkflowInvalidWorkflowException(
              sprintf(
                'Node form "%s" has one response required and min_response is not 1. Please set 1 for min and max.',
                $this->getInternalName()
              )
            );
    	}

    	//plusieurs réponses avec auto enable
    	if ($this->getMinResponse() < $this->getMaxResponse() && !$this->doConfirmContinue()) {
    		throw new WorkflowInvalidWorkflowException(
              sprintf(
                'Node form "%s" has many response required and auto_continue enable. Please disable it.',
                $this->getInternalName()
              )
            );
    	}
    	
    }

} // END class WorkflowNodeForm