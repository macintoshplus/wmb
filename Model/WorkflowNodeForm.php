<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsBool;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsInstanceOf;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException;
use JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter\NodeVoterInterface;

/**
 * WorkflowNodeForm class
 * Multiple response for one form.
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeForm extends WorkflowNode implements NodeVoterInterface
{
    const PREFIX_RESPONSE = '_response';
    const PREFIX_CONTINUE = '_continue';
    const PREFIX_REVIEW = '_review';
    const PREFIX_DELETED = '_deleted';

    protected $configuration = array(
        'min_response' => 1,
        'max_response' => false,
        'internal_name' => null,
        'auto_continue' => false,
        'roles' => null,
       );

    protected $maxInNodes = false;

    public function __construct(array $configuration)
    {
        if (!isset($configuration['min_response']) || !is_integer($configuration['min_response']) || 1 > $configuration['min_response']) {
            $configuration['min_response'] = $this->configuration['min_response'];
        }

        if (!isset($configuration['max_response']) || (!is_integer($configuration['max_response']) && !is_bool($configuration['max_response']))) {
            $configuration['max_response'] = $this->configuration['max_response'];
        }

        if (false !== $configuration['max_response'] && $configuration['max_response'] < $configuration['min_response']) {
            $configuration['min_response'] = $configuration['max_response'];
        }

        if (!isset($configuration['auto_continue']) || (!is_bool($configuration['auto_continue']))) {
            $configuration['auto_continue'] = $this->configuration['auto_continue'];
        }

        if (!isset($configuration['internal_name']) || null === $configuration['internal_name']) {
            $configuration['internal_name'] = sprintf('form_%d', time());
        }

        if (!isset($configuration['roles'])) {
            $configuration['roles'] = null;
        }

        parent::__construct($configuration);
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     *
     * @return array
     * @ignore
     */
    public static function configurationFromXML(\DOMElement $element)
    {
        $configuration = array(
          'min_response' => intval($element->getAttribute('min_response')),
          'max_response' => (($element->getAttribute('max_response') == '0') ? false : intval($element->getAttribute('max_response'))),
          'internal_name' => $element->getAttribute('internal_name'),
          'auto_continue' => ($element->getAttribute('auto_continue') == 'true'),
          'roles' => explode(',', $element->getAttribute('roles')),
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
        $element->setAttribute('min_response', sprintf('%d', $this->configuration['min_response']));
        $element->setAttribute('max_response', sprintf('%d', $this->configuration['max_response']));
        $element->setAttribute('internal_name', $this->configuration['internal_name']);
        $element->setAttribute('auto_continue', ($this->configuration['auto_continue'] ? 'true' : 'false'));
        $element->setAttribute('roles', implode(',', $this->configuration['roles']));
    }

    /**
     * return internal name
     * this name is use when ID for Type Form link.
     *
     * @return string
     */
    public function getInternalName()
    {
        return $this->configuration['internal_name'];
    }

    /**
     * @param string $name
     *
     * @return WorkflowNodeForm
     */
    public function setInternalName($name)
    {
        $this->configuration['internal_name'] = $name;

        return $this;
    }

    /**
     * Return true if auto continue is disabled.
     *
     * @return bool
     */
    public function doConfirmContinue()
    {
        //Si plusieurs quelque soit auto_continue
        // ou auto_continue a faux
        return (false === $this->configuration['max_response'] || 1 < $this->configuration['max_response'] || !$this->configuration['auto_continue']);
    }

    public function doSingleResponse()
    {
        return ($this->configuration['min_response'] === $this->configuration['max_response'] && 1 === $this->configuration['min_response']);
    }

    /**
     * @return int
     */
    public function getMinResponse()
    {
        return $this->configuration['min_response'];
    }

    /**
     * @param int $min
     *
     * @return WorkflowNodeForm
     */
    public function setMinResponse($min)
    {
        if (!is_integer($min)) {
            throw new BaseValueException('max_response', $min, 'WorkflowNodeForm');
        }
        $this->configuration['min_response'] = $min;

        return $this;
    }

    /**
     * @return false|int
     */
    public function getMaxResponse()
    {
        return $this->configuration['max_response'];
    }

    /**
     * @param int|false $max
     *
     * @return WorkflowNodeForm
     */
    public function setMaxResponse($max)
    {
        if ((!is_integer($max) && !is_bool($max)) || (is_bool($max) && false !== $max)) {
            throw new BaseValueException('max_response', $max, 'WorkflowNodeForm');
        }
        $this->configuration['max_response'] = $max;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoContinue()
    {
        return $this->configuration['auto_continue'];
    }

    /**
     * @param bool $autoContinue
     *
     * @return WorkflowNodeForm
     */
    public function setAutoContinue($autoContinue)
    {
        if (!is_bool($autoContinue)) {
            throw new BaseValueException('auto_continue', $autoContinue, 'WorkflowNodeForm');
        }
        $this->configuration['auto_continue'] = $autoContinue;

        return $this;
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
     *
     * @return WorkflowNodeForm
     */
    public function setRoles(array $roles = null)
    {
        $this->configuration['roles'] = $roles;

        return $this;
    }

    /**
     * return true if the response count is valid.
     *
     * @return bool
     */
    public function responseIsEnough(WorkflowExecution $execution)
    {
        $nbResponse = count($this->getResponses($execution));
        //réponse unique
        if ($this->doSingleResponse() && 1 === $nbResponse) {
            return true;
        }
        //réponse multiple
        //nombre de réponse inférieur au minimum
        if ($this->getMinResponse() > $nbResponse) {
            return false;
        }
        //nb de réponse supérieur au minimum, inférieur ou égaleme au max ?
        //Cas du max = faux : répond toujour vrai
        if (false === $this->getMaxResponse()) {
            return true;
        }
        //Cas du max <= nb
        if ($this->getMaxResponse() >= $nbResponse) {
            return true;
        }
        //Par défaut
        //Cas du : trop de réponse
        return false;
    }

    public function getResponses(WorkflowExecution $execution)
    {
        return $execution->getVariable($this->configuration['internal_name']);
    }

    /**
     * return true if username si in roles.
     *
     * @param string $username
     *
     * @return bool
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
        $formName = $this->configuration['internal_name'];
        $formNameResponse = $formName.self::PREFIX_RESPONSE;
        $formNameContinue = $formName.self::PREFIX_CONTINUE;
        $formNameReview = $formName.self::PREFIX_REVIEW;
        $formNameDeleted = $formName.self::PREFIX_DELETED;
        //$variables = $execution->getVariables();

        //Vérifie que les données sont renseignées
        if (!$execution->hasVariable($formName)) {
            $execution->setVariable($formName, array());
            $canExecute = false;
            $execution->debug('Add variable');
        }

        //Vérifie si une réponse a été fournie
        if ($execution->hasVariable($formNameResponse)) {
            $response = $execution->getVariable($formNameResponse);

            //Vérifie si il y a une review, si oui, il supprime
            if ($execution->hasVariable($formNameReview)) {
                $execution->unsetVariable($formNameReview);
            }

            //Déplace les données
            $responses = $this->getResponses($execution);

            //Si un ID est fourni, il l'extrait
            if (null !== $response->getId()) {
                //récupère la clée
                $key = $response->getId();

                //si aucune données n'est présente pour cette clé, elle est effacé.
                if (!array_key_exists($key, $responses)) {
                    unset($key);
                }
            }

            //Si données supprimées, il supprime la réponse et ajoute dans les suppressions
            if (null !== $response->getDeletedAt()) {
                if (!isset($key)) {
                    $canExecute = false;
                    goto traiteExecution;
                }
                unset($responses[$key]);

                $responsesDeleted = ($execution->hasVariable($formNameDeleted)) ? $execution->getVariable($formNameDeleted) : array();
                $responsesDeleted[] = $response;
                $execution->setVariable($formNameDeleted, $responsesDeleted);

                $execution->info('Delete response !');
            } else {
                //Pas supprimé

                //Si c'est une modification
                if (null !== $response->getAnsweredAt()) {
                    $response->setUpdatedAt(new \DateTime());
                }
                //si c'est un ajout
                if (null === $response->getAnsweredAt()) {
                    $response->setAnsweredAt(new \DateTime());
                }
                //si une réponse possible, il ajoute les données en remplaçant celle eventuellement présente
                if ($this->doSingleResponse()) {
                    $responses = array($response);
                    $execution->info('Single response set !');
                } else {
                    //Sinon, si la clée est initialisée, remplacement des données, sinon, ajout
                    if (isset($key)) {
                        $responses[$key] = $response;
                        $execution->info('Response updated !');
                    } else {
                        if (false === $this->getMaxResponse() || count($responses) < $this->getMaxResponse()) {
                            $responses[substr(uniqid(), -8)] = $response;
                            $execution->info(sprintf('Multiple response : Response add (count = %d) !', count($responses)));
                        } else {
                            $execution->warning(sprintf('Multiple response : Response not add (count = %d) !', count($responses)));
                        }
                    }
                }
            }

            //reset id
            foreach ($responses as $key => $response) {
                $responses[$key]->setId($key);
            }

            $execution->setVariable($formName, $responses);
            //supprime la réponses
            $execution->unsetVariable($formNameResponse);

            //Si il n'y a un nombre suffisent de réponse, il faut passer automatiquement à la suite.
            if ($this->responseIsEnough($execution) && $this->configuration['auto_continue']) {
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

        traiteExecution:
        if (!$canExecute) {
            //echo "Add variables\n";
            //Ajoute la variable de réponse
            $execution->addWaitingFor($this, $formNameResponse, new WorkflowConditionIsInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface'));
            //Si plusieurs ou auto réponse a faux
            if ($this->doConfirmContinue()) {
                $execution->addWaitingFor($this, $formNameContinue, new WorkflowConditionIsBool());
            }

            return false;
        }

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);
    }

    public function verify()
    {
        parent::verify();

        //min > max
        if (false !== $this->getMaxResponse() && $this->getMinResponse() > $this->getMaxResponse()) {
            throw new WorkflowInvalidWorkflowException(
                sprintf(
                    'Node form "%s" has min response greater than max response.',
                    $this->getInternalName()
                )
            );
        }

        //plusieurs réponses avec auto enable
        if (false === $this->doSingleResponse() && true === $this->getAutoContinue()) {
            throw new WorkflowInvalidWorkflowException(
                sprintf(
                    'Node form "%s" has many response required and auto_continue enable. Please disable it.',
                    $this->getInternalName()
                )
            );
        }
    }
}
