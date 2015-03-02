<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Twig_Environment;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;

/**
 * WorkflowNodeComputeExecutionName class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeComputeExecutionName extends WorkflowNode
{

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration = null)
    {

        if (!isset($configuration['template'])) {
            $configuration['template'] = "Execution name {{execution_id}}";
        }

        parent::__construct($configuration);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->configuration['template'];
    }

    /**
     * @param string $template
     * @return WorkflowNodeComputeExecutionName
     */
    public function setTemplate($template)
    {
        if (!is_string($template)) {
            throw new BaseValueException('template', $template, 'WorkflowNodeComputeExecutionName');
        }
        $this->configuration['template'] = $template;

        return $this;
    }
    public function execute(WorkflowExecution $execution)
    {

        if (!$execution->hasTwig()) {
            throw new \Exception("Enable to use this node if twig service is not set");
        }

        $array = $execution->getVariables();
        $array['execution_id'] = $execution->getId();
        $array['workflow_name'] = $execution->workflow->name;
        $array['workflow_id'] = $execution->workflow->id;
        $array['users_name'] = '';
        if (null !== $execution->getRoles() && 0 < count($execution->getRoles())) {
            $roles = $execution->getRoles();
            $name = '';
            foreach ($roles as $role) {
                $name .= (('' === $name)? '':', ').$role->__toString();
            }
            $array['users_name'] = $name;
        }
        
        $name = $execution->renderTemplate($this->configuration['template']);

        $execution->setName($name);

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);

    }
}
