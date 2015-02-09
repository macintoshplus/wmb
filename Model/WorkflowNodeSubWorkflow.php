<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;

/**
 * An object of the WorkflowNodeSubWorkflow class represents a sub-workflow.
 *
 * When the node is reached during execution of the workflow, the specified sub-workflow
 * is started. The original workflow is suspended until the sub-workflow has finished executing.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * The example below creates a sub-workflow node that passes the parent
 * execution's variable 'x' to the variable 'y' in the child execution when the
 * sub-workflow is started. When it ends, the child execution's 'y' variable is
 * passed to the parent execution as 'z'.
 *
 * <code>
 * <?php
 * $subWorkflow = new WorkflowNodeSubWorkflow(
 *   array(
 *     'workflow'  => 'IncrementVariable',
 *     'variables' => array(
 *       'in' => array(
 *         'x' => 'y'
 *       ),
 *       'out' => array(
 *         'y' => 'z'
 *       )
 *     )
 *   )
 *);
 * ?>
 * </code>
 *
 */
class WorkflowNodeSubWorkflow extends WorkflowNode
{
    /**
     * Execution ID of the sub workflow,
     * 0 if it has not been started yet.
     *
     * @var integer
     */
    protected $state = 0;

    /**
     * Constructs a new sub workflow with the configuration $configuration.
     *
     * Configuration format
     * <ul>
     * <li>
     *   <b>String:</b>
     *   The name of the workflow to execute. The workflow is loaded using the
     *   loadByName method on the execution engine.
     * </li>
     *
     * <li>
     *   <b>Array:</b>
     *   <ul>
     *     <li><i>workflow:</i> The name of the workflow to execute. The workflow
     *     is loaded using the loadByName method on the execution engine.</li>
     *     <li><i>variables:</i> An array with the information for mapping
     *     workflow variables between parent and child workflow execution.</li>
     *   </ul>
     * <li>
     * </ul>
     *
     * @param mixed $configuration
     */
    public function __construct($configuration)
    {
        if (is_string($configuration)) {
            $configuration = array('workflow' => $configuration);
        }

        if (!isset($configuration['variables'])) {
            $configuration['variables'] = array(
                'in' => array(), 'out' => array()
            );
        }

        parent::__construct($configuration);
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
        if ($execution->definitionStorage === null) {
            throw new WorkflowExecutionException(
                'No WorkflowDefinitionStorage implementation available.'
            );
        }

        $workflow = $execution->definitionStorage->loadByName($this->configuration['workflow']);

        // Sub Workflow is not interactive.
        if (!$workflow->isInteractive() && !$workflow->hasSubWorkflows()) {
            $subExecution = $execution->getSubExecution(null, false);
            $subExecution->workflow = $workflow;

            $this->passVariables(
                $execution, $subExecution, $this->configuration['variables']['in']
            );

            $subExecution->start();
        } else {
        // Sub Workflow is interactive.
        
            // Sub Workflow is to be started.
            if ($this->state == 0) {
                $subExecution = $execution->getSubExecution();
                $subExecution->workflow = $workflow;

                $this->passVariables(
                  $execution, $subExecution, $this->configuration['variables']['in']
                );

                $subExecution->start($execution->getId());

                $this->state = $subExecution->getId();
            } else {
            // Sub Workflow is to be resumed.
                $subExecution = $execution->getSubExecution($this->state);
                $subExecution->workflow = $workflow;
                $subExecution->resume($execution->getVariables());
            }
        }

        // Execution of Sub Workflow was cancelled.
        if ($subExecution->isCancelled()) {
            $execution->cancel($this);
        }

        // Execution of Sub Workflow has ended.
        if ($subExecution->hasEnded()) {
            $this->passVariables(
                $subExecution, $execution, $this->configuration['variables']['out']
            );

            $this->activateNode($execution, $this->outNodes[0]);

            $this->state = 0;

            return parent::execute($execution);
        }

        // Execution of Sub Workflow has been suspended.
        foreach ($subExecution->getWaitingFor() as $variableName => $data) {
            $execution->addWaitingFor($this, $variableName, $data['condition']);
        }

        return false;
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
            'workflow'  => $element->getAttribute('subWorkflowName'),
            'variables' => array(
            'in' => array(), 'out' => array()
            )
        );

        $xpath = new \DOMXPath($element->ownerDocument);
        $in    = $xpath->query('in/variable', $element);
        $out   = $xpath->query('out/variable', $element);

        foreach ($in as $variable) {
            $configuration['variables']['in'][$variable->getAttribute('name')] = $variable->getAttribute('as');
        }

        foreach ($out as $variable) {
            $configuration['variables']['out'][$variable->getAttribute('name')] = $variable->getAttribute('as');
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
        $element->setAttribute('subWorkflowName', $this->configuration['workflow']);

        if (!empty($this->configuration['variables']['in'])) {
            $in = $element->appendChild(
                $element->ownerDocument->createElement('in')
            );

            foreach ($this->configuration['variables']['in'] as $fromName => $toName) {
                $variable = $in->appendChild(
                    $in->ownerDocument->createElement('variable')
                );

                $variable->setAttribute('name', $fromName);
                $variable->setAttribute('as', $toName);
            }
        }

        if (!empty($this->configuration['variables']['out'])) {
            $out = $element->appendChild(
                $element->ownerDocument->createElement('out')
            );

            foreach ($this->configuration['variables']['out'] as $fromName => $toName) {
                $variable = $out->appendChild(
                    $out->ownerDocument->createElement('variable')
                );

                $variable->setAttribute('name', $fromName);
                $variable->setAttribute('as', $toName);
            }
        }
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'Sub Workflow: ' . $this->configuration['workflow'];
    }

    /**
     * Passes variables from one execution context to another.
     *
     * @param  WorkflowExecution $from The execution context the variables are passed from.
     * @param  WorkflowExecution $to The execution context the variables are passed to.
     * @param  array                $variables The names of the variables.
     * @throws WorkflowExecutionException if a variable that is to be passed does not exist.
     * @ignore
     */
    protected function passVariables(WorkflowExecution $from, WorkflowExecution $to, array $variables)
    {
        foreach ($variables as $fromName => $toName) {
            $to->setVariable($toName, $from->getVariable($fromName));
        }
    }
}
?>
