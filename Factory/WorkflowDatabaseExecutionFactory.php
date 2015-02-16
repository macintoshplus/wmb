<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Factory;

use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution;
use JbNahan\Bundle\WorkflowManagerBundle\Manager\

DefinitionManager;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
* Execution factory
*/
class WorkflowDatabaseExecutionFactory
{
    /**
     *

DefinitionManager
     *
     * @var

DefinitionManager
     **/
    private $definitionService;

    /**
     * @var EntityManager
     **/
    private $entityManager;

    /**
     * SecurityContextInterface
     *
     * @var SecurityContextInterface
     **/
    private $security;


    public function __construct(EntityManager $entityManager,

DefinitionManager $definitionService, SecurityContextInterface $security)
    {
        $this->definitionService = $definitionService;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @param int $id
     * @return WorkflowDatabaseExecution
     */
    public function executionById($id)
    {
        $execution = new WorkflowDatabaseExecution($this->entityManager, $this->definitionService, $this->security, $id);
        return $execution;

    }

    /**
     * @param int $workflowId
     * @return WorkflowDatabaseExecution
     */
    public function executionByWorkflowId($workflowId)
    {
        $wf = $this->definitionService->loadById($workflowId);
        if (!$wf->isPublished()) {
            throw new WorkflowExecutionException("Unable to create new execution for a unpublished definition");
        }
        if ($wf->isArchived()) {
            throw new WorkflowExecutionException("Unable to create new execution for a archived definition");
        }
        $execution = new WorkflowDatabaseExecution($this->entityManager, $this->definitionService, $this->security);
        $execution->workflow = $wf;
        return $execution;
    }
}
