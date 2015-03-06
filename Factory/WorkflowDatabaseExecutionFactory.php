<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Factory;

use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowExternalCounterInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Manager\DefinitionManager;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Psr\Log\LoggerInterface;
use \Swift_Mailer;
use \Twig_Environment;

/**
* Execution factory
*/
class WorkflowDatabaseExecutionFactory
{
    /**
     * DefinitionManager
     *
     * @var DefinitionManager
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

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var WorkflowExternalCounterInterface
     */
    protected $counterManager;


    public function __construct(EntityManager $entityManager, DefinitionManager $definitionService, SecurityContextInterface $security, LoggerInterface $logger, Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this->definitionService = $definitionService;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function setCounterManager(WorkflowExternalCounterInterface $counterManager)
    {
        $this->counterManager = $counterManager;
    }

    /**
     * @param int $id
     * @return WorkflowDatabaseExecution
     */
    public function executionById($id)
    {
        $execution = new WorkflowDatabaseExecution($this->entityManager, $this->definitionService, $this->security, $this->logger, $this->mailer, $this->twig, $this->counterManager, $id);
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
        $execution = new WorkflowDatabaseExecution($this->entityManager, $this->definitionService, $this->security, $this->logger, $this->mailer, $this->twig, $this->counterManager);
        $execution->workflow = $wf;
        return $execution;
    }
}
