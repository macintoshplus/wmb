<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * Interface for workflow definition storage handlers.
 *
 */
interface WorkflowDefinitionStorageInterface
{
    /**
     * Load a workflow definition by name.
     *
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return Workflow
     * @throws WorkflowDefinitionStorageException
     */
    public function loadByName( $workflowName, $workflowVersion = 0 );

    /**
     * Save a workflow definition to the database.
     *
     * @param  Workflow $workflow
     * @throws WorkflowDefinitionStorageException
     */
    public function save( Workflow $workflow );
}

