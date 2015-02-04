<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyNotFoundException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Entity as Entity;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowExecution;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseOptions;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNode;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Manager\WorkflowDatabaseDefinitionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Workflow executer that suspends and resumes workflow
 * execution states to and from a database.
 *
 */
class WorkflowDatabaseExecution extends WorkflowExecution
{
    /**
     * EntityManagerInterface instance to be used.
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;


    /**
     * Flag that indicates whether the execution has been loaded.
     *
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'definitionStorage' => null,
      'workflow' => null,
      'options' => null
    );

    /**
     * Construct a new database execution.
     *
     * This constructor is a tie-in.
     *
     * @param  EntityManagerInterface $entityManager
     * @param  int          $executionId
     * @throws WorkflowExecutionException
     */
    public function __construct ( EntityManagerInterface $entityManager, WorkflowDefinitionStorageInterface $definitionService, SecurityContextInterface $security, $executionId = null )
    {
        if ( $executionId !== null && !is_int( $executionId ) )
        {
            throw new WorkflowExecutionException( '$executionId must be an integer.' );
        }

        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->properties['definitionStorage'] = $definitionService;
        $this->properties['options'] = new WorkflowDatabaseOptions;

        if ( is_int( $executionId ) )
        {
            $this->loadExecution( $executionId );
        }
    }

    /**
     * Property get access.
     *
     * @param string $propertyName
     * @return mixed
     * @throws BasePropertyNotFoundException
     *         If the given property could not be found.
     * @ignore
     */
    public function __get( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'definitionStorage':
            case 'workflow':
            case 'options':
                return $this->properties[$propertyName];
        }

        throw new BasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property set access.
     *
     * @param string $propertyName
     * @param string $propertyValue
     * @throws BasePropertyNotFoundException
     *         If the given property could not be found.
     * @throws BaseValueException
     *         If the value for the property options is not an WorkflowDatabaseOptions object.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'definitionStorage':
            case 'workflow':
                return parent::__set( $propertyName, $propertyValue );
            case 'options':
                if ( !( $propertyValue instanceof WorkflowDatabaseOptions ) )
                {
                    throw new BaseValueException(
                        $propertyName,
                        $propertyValue,
                        'WorkflowDatabaseOptions'
                    );
                }
                break;
            default:
                throw new BasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName
     * @return bool
     * @ignore
     */
    public function __isset( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'definitionStorage':
            case 'workflow':
            case 'options':
                return true;
        }

        return false;
    }

    /**
     * Start workflow execution.
     *
     * @param  int $parentId
     */
    protected function doStart( $parentId )
    {

        $execution = new Entity\Execution();


        $execution->setWorkflow((int)$this->workflow->id);
        $execution->setParent((int)$parentId);
        $execution->setVariables(WorkflowDatabaseDefinitionStorage::serialize( $this->variables ));
        $execution->setWaitingfor(WorkflowDatabaseDefinitionStorage::serialize( $this->waitingFor ));
        $execution->setThreads(WorkflowDatabaseDefinitionStorage::serialize( $this->threads ));
        $execution->setNextThreadId((int)$this->nextThreadId);
        $execution->setCancellable($this->isCancellable());
        $token = $this->security->getToken();
        $execution->setStartedBy((is_object($token))? $token->getUsername():'Anonymous');

        $this->entityManager->persist($execution);
        $this->entityManager->flush();

        $this->id = $execution->getId();
    }

    /**
     * Suspend workflow execution.
     *
     */
    protected function doSuspend()
    {

        $this->cleanupTable( 'execution_state' );
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Execution')->getExecutionById($this->id);
        if ( $result === false || empty( $result ) )
        {
            throw new WorkflowExecutionException(
              'Could not Suspend execution.'
            );
        }
        $execution = $result[0];
        
        $execution->setVariables(WorkflowDatabaseDefinitionStorage::serialize( $this->variables ));
        $execution->setWaitingfor(WorkflowDatabaseDefinitionStorage::serialize( $this->waitingFor ));
        $execution->setThreads(WorkflowDatabaseDefinitionStorage::serialize( $this->threads ));
        $execution->setNextThreadId((int)$this->nextThreadId);
        $execution->setSuspendedAt(new \DateTime());
        $execution->setName($this->getName());
        $execution->setCancellable($this->isCancellable());
        $execution->setRoles($this->getRoles());
        $description = '';
        foreach ( $this->activatedNodes as $node )
        {
            if (null !== $node->getName()) {
                $description .= (empty($description)? '':', ') . $node->getName();
            }
            $state = new Entity\ExecutionState();
            $state->setExecution($execution);
            $state->setNode($node->getId());
            $state->setNodeState(WorkflowDatabaseDefinitionStorage::serialize( $node->getState() ) );
            $state->setNodeActivatedFrom(WorkflowDatabaseDefinitionStorage::serialize( $node->getActivatedFrom() ) );
            $state->setNodeThreadId((int)$node->getThreadId());
            $execution->addState($state);

            $this->entityManager->persist($state);
        }
        $execution->setSuspendedStep($description);

        $this->entityManager->persist($execution);
        $this->entityManager->flush();
    }

    /**
     * Resume workflow execution.
     *
     */
    protected function doResume()
    {
        //$this->db->beginTransaction();
    }

    /**
     * End workflow execution.
     *
     */
    protected function doEnd()
    {
        //Enregistre l'état
        $this->doSuspend();

        //traite la fin : annulée | fin de l'éxécution
        $result = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Execution')->getExecutionById($this->id);
        if ( $result === false || empty( $result ) )
        {
            throw new WorkflowExecutionException(
              'Could not End execution.'
            );
        }

        $token = $this->security->getToken();
        $user = (is_object($token))? $token->getUsername():'Anonymous';

        $execution = $result[0];
        $execution->setSuspendedAt(null);
        if ( !$this->isCancelled() ) {
            $this->cleanupTable( 'execution_state' );
            $execution->setSuspendedStep(null);
            $execution->setEndAt(new \DateTime());
            $execution->setEndBy($user);
        } else {
            $execution->setCanceledAt(new \DateTime());
            $execution->setCanceledBy($user);
        }
        $this->entityManager->flush();

    }

    /**
     * Returns a new execution object for a sub workflow.
     *
     * @param  int $id
     * @return WorkflowExecution
     */
    protected function doGetSubExecution( $id = null )
    {
        return new WorkflowDatabaseExecution( $this->entityManager, $this->properties['definitionStorage'], $id );
    }

    /**
     * Cleanup execution / execution_state tables.
     *
     * @param  string $tableName
     */
    protected function cleanupTable( $tableName )
    {
        $result = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Execution')->getExecutionById($this->getId());

        if (!empty($result)) {
            $wf = $result[0];
            if ('execution_state' === $tableName) {
                foreach ($wf->getStates() as $state) {
                    $this->entityManager->remove($state);
                }
            }
        }
    }

    /**
     * Load execution state.
     *
     * @param int $executionId  ID of the execution to load.
     * @throws WorkflowExecutionException
     */
    protected function loadExecution( $executionId )
    {
        $result = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Execution')->getExecutionById($executionId);

        if ( $result === false || empty( $result ) )
        {
            throw new WorkflowExecutionException(
              'Could not load execution state.'
            );
        }
        
        $wf = $result[0];
        $this->id = $executionId;

        $this->nextThreadId = $wf->getNextThreadId();
        $this->roles = $wf->getRoles();
        $this->setCancellable($wf->getCancellable());
        $this->setName($wf->getName());
        $this->threads = WorkflowDatabaseDefinitionStorage::unserialize( $wf->getThreads() );
        $this->variables = WorkflowDatabaseDefinitionStorage::unserialize( $wf->getVariables() );
        $this->waitingFor = WorkflowDatabaseDefinitionStorage::unserialize( $wf->getWaitingFor() );

        $active = array();


        //foreach ( $result as $row )
        foreach ($wf->getStates() as $state)
        {
            $active[$state->getNode()] = array(
              'activated_from' => WorkflowDatabaseDefinitionStorage::unserialize($state->getNodeActivatedFrom()),
              'state' => WorkflowDatabaseDefinitionStorage::unserialize($state->getNodeState(), null),
              'thread_id' => $state->getNodeThreadId()
            );
        }

        $workflowId     = $wf->getWorkflow();
        $this->workflow = $this->properties['definitionStorage']->loadById( $workflowId );

        foreach ( $this->workflow->nodes as $node )
        {
            $nodeId = $node->getId();

            if ( isset( $active[$nodeId] ) )
            {
                $node->setActivationState( WorkflowNode::WAITING_FOR_EXECUTION );
                $node->setThreadId( $active[$nodeId]['thread_id'] );
                $node->setState( $active[$nodeId]['state'], null );
                $node->setActivatedFrom( $active[$nodeId]['activated_from'] );

                $this->activate( $node, false );
            }
        }

        //si canceled != null alors il est annulée
        $this->cancelled = (null !== $wf->getCanceledAt());
        //si end != null alors il est terminée
        $this->ended     = (null !== $wf->getEndAt());
        
        $this->loaded    = true;
        $this->resumed   = false;
        //Si suspendu est === null il n'est pas suspendu
        $this->suspended = (null !== $wf->getSuspendedAt());
    }
}

