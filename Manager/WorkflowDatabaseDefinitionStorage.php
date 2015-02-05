<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Manager;

use JbNahan\Bundle\WorkflowManagerBundle\Entity as Entity;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use \Twig_Environment;
use \Swift_Mailer;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowDefinitionStorageException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyNotFoundException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowExecution;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseOptions;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNode;
use JbNahan\Bundle\WorkflowManagerBundle\Model\BaseWorkflowDefinitionStorage;
use JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFinally;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeComputeExecutionName;

/**
 * Workflow definition storage handler that saves and loads workflow
 * definitions to and from a database.
 *
 */
class WorkflowDatabaseDefinitionStorage extends BaseWorkflowDefinitionStorage
{
    /**
     * EntityManager instance to be used.
     *
     * @var EntityManager
     */
    protected $entityManager;

    protected $security;

    protected $twig;

    protected $mailer;

    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array('options' => null);

    /**
     * Construct a new database definition handler.
     *
     * This constructor is a tie-in.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, SecurityContextInterface $security, Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->mailer = $mailer;
        $this->twig = $twig;

        $this->properties['options'] = new WorkflowDatabaseOptions;
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
    public function __get($propertyName)
    {
        switch ($propertyName)
        {
            case 'options':
                return $this->properties[$propertyName];
        }

        throw new BasePropertyNotFoundException($propertyName);
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
    public function __set($propertyName, $propertyValue)
    {
        switch ($propertyName)
        {
            case 'options':
                if (!($propertyValue instanceof WorkflowDatabaseOptions)) {
                    throw new BaseValueException(
                        $propertyName,
                        $propertyValue,
                        'WorkflowDatabaseOptions'
                    );
                }
                break;
            default:
                throw new BasePropertyNotFoundException($propertyName);
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
    public function __isset($propertyName)
    {
        switch ($propertyName)
        {
            case 'options':
                return true;
        }

        return false;
    }

    /**
     * Load a workflow definition by ID.
     *
     * Providing the name of the workflow that is to be loaded as the
     * optional second parameter saves a database query.
     *
     * @param  int $workflowId
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return Workflow
     * @throws WorkflowDefinitionStorageException
     * 
     */
    public function loadById($workflowId, $workflowName = '', $workflowVersion = 0)
    {
        // Query the database for the name and version of the workflow.
        $repo = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Definition');
        $workflowsDb = $repo->findById($workflowId);
        if (!empty($workflowsDb)) {
            $workflowDb = $workflowsDb[0];
        } else {
            throw new WorkflowDefinitionStorageException('Could not load workflow definition.');
        }

        if (empty($workflowName) || $workflowVersion == 0) {
            $workflowName    = $workflowDb->getName();
            $workflowVersion = $workflowDb->getVersion();
        }

        $nodes  = array();
        $nodesDb = $workflowDb->getNodes();

        foreach ($nodesDb as $nodeDb) {
            $configuration = self::unserialize($nodeDb->getConfiguration(), null);
            if (is_null($configuration)) {
                  $configuration = self::getDefaultConfiguration($nodeDb->getClass());
            }
            //création de l'objet
            $classname = $nodeDb->getClass();
            $nodeDbId = $nodeDb->getId();

            $nodes[$nodeDbId] = new $classname($configuration);
            $nodes[$nodeDbId]->setName($nodeDb->getName());

            if ($nodes[$nodeDbId] instanceof WorkflowNodeFinally && !isset($finallyNode)) {
                $finallyNode = $nodes[$nodeDbId];
            } else if ($nodes[$nodeDbId] instanceof WorkflowNodeEnd && !isset($defaultEndNode)) {
                $defaultEndNode = $nodes[$nodeDbId];
            } else if ($nodes[$nodeDbId] instanceof WorkflowNodeStart && !isset($startNode)) {
                $startNode = $nodes[$nodeDbId];
            }
            if ($nodes[$nodeDbId] instanceof WorkflowNodeEmail) {
                $nodes[$nodeDbId]->setMailer($this->mailer);
                $nodes[$nodeDbId]->setTwig($this->twig);
            }
            if ($nodes[$nodeDbId] instanceof WorkflowNodeComputeExecutionName) {
                $nodes[$nodeDbId]->setTwig($this->twig);
            }
        }

        

        if (!isset($startNode) || !isset($defaultEndNode)) {
            throw new WorkflowDefinitionStorageException('Could not load workflow definition.');
        }

        //chargement des connections
        $connectionRepo = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:NodeConnection');
        $connectionsDb = $connectionRepo->getConnectionByWorkflowId($workflowId);

        foreach ($connectionsDb as $connection) {
            $nodes[$connection->getIncomingNode()->getId()]->addOutNode($nodes[$connection->getOutgoingNode()->getId()]);
        }

        if (!isset($finallyNode) || count($finallyNode->getInNodes()) > 0) {
            $finallyNode = null;
        }


        //création du wf
        $workflow = new Workflow($workflowName, $startNode, $defaultEndNode, $finallyNode);
        $workflow->definitionStorage = $this;
        $workflow->id = (int)$workflowId;
        $workflow->version = (int)$workflowVersion;
        $workflow->setPublishedAt($workflowDb->getPublishedAt());
        $workflow->setArchivedAt($workflowDb->getArchivedAt());
        $workflow->setParent($workflowDb->getParent());
        $workflow->setRolesForUse($workflowDb->getRolesForUse());
        $workflow->setRolesForUpdate($workflowDb->getRolesForUpdate());

        // Verify the loaded workflow.
        $workflow->verify();

        return $workflow;
        
    }

    /**
     * Load a workflow definition by name.
     *
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return Workflow
     * @throws WorkflowDefinitionStorageException
     * 
     */
    public function loadByName($workflowName, $workflowVersion = 0)
    {

        // Load the current version of the workflow.
        if ($workflowVersion == 0) {
            $workflowVersion = $this->getCurrentVersionNumber($workflowName);
        }
        
        $repo = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Definition');
        $qb = $repo->createQueryBuilder('w');
        $qb->select('w.id')
        ->where('w.name = :name')
        ->andWhere('w.version = :version')
        ->setParameter('name', $workflowName)
        ->setParameter('version', $workflowVersion);


        $result = $qb->getQuery()->getResult(\PDO::FETCH_ASSOC);
        
        if ($result !== false && isset($result[0])) {
            return $this->loadById(
                $result[0]['id'],
                $workflowName,
                $workflowVersion
            );
        } else {
            throw new WorkflowDefinitionStorageException(
                'Could not load workflow definition.'
            );
        }
    }

    /**
     * Save a workflow definition to the database.
     *
     * @param  Workflow $workflow
     * @return Workflow
     * @throws WorkflowDefinitionStorageException
     */
    public function save(Workflow $workflow)
    {
        // Verify the workflow.
        $workflow->verify();

        $id = $workflow->id;
        if (false !== $id) {
            $dbDefinitions = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Definition')->findById($id);
            if (empty($dbDefinitions)) {
                $id = false;
            } else {
                $dbDefinition = $dbDefinitions[0];
                $token = $this->security->getToken();
                $dbDefinition->setUpdatedBy((is_object($token))? $token->getUsername():'Anonymous');
            }
        }
        //Si le WF est chargé depuis la DB effacement des connexions et des nodes
        $nodeRepo = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Node');
        $connectionRepo = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:NodeConnection');
        if (isset($dbDefinition)) {
            $connectionsDb = $connectionRepo->getConnectionByWorkflowId($dbDefinition->getId());

            foreach ($connectionsDb as $oldConnectionDb) {
                $this->entityManager->remove($oldConnectionDb);
            }

            $dbNodes = $nodeRepo->findBy(array('workflow'=>$dbDefinition->getId()));
            foreach ($dbNodes as $oldNodeDb) {
                $this->entityManager->remove($oldNodeDb);
            }
        }

        //Le WF est nouveau
        if (false === $id) {
            $dbDefinition = new Entity\Definition();
            $token = $this->security->getToken();
            $dbDefinition->setCreatedBy((is_object($token))? $token->getUsername():'Anonymous');
        }

        //Set des propriétés
        $dbDefinition->setName($workflow->name);
        $dbDefinition->setVersion($workflow->version);
        $dbDefinition->setParent($workflow->getParent());
        $dbDefinition->setRolesForUse($workflow->getRolesForUse());
        $dbDefinition->setRolesForUpdate($workflow->getRolesForUpdate());

        $nodes = $workflow->nodes;
        $nodeMap = array();
        foreach ($nodes as $key => $node) {
            $nodeId = $node->getId();
            //Charge si le wf est déjà en db ou créer un nouvel element
            /*if (false !== $nodeId && false !== $dbDefinition->getId()) {
                $dbNodes = $nodeRepo->findBy(array('id'=>$nodeId,'workflow'=>$dbDefinition->getId()));
                if (empty($dbNodes)) {
                    $nodeId = false;
                } else {
                    $dbNode = $dbNodes[0];
                }
            } */
            //if (false === $nodeId) {
            $dbNode = new Entity\Node();
            $dbNode->setDefinition($dbDefinition);
            $dbNode->setName($node->getName());
            //}
            //Set data
            $dbNode->setClass(get_class($node));
            $dbNode->setConfiguration(self::serialize($node->getConfiguration()));
            $dbDefinition->addNode($dbNode);
            $nodeMap[]=array('db'=>$dbNode, 'node'=>$node);
            //persist
            $this->entityManager->persist($dbNode);
        }

        foreach ($nodes as $key => $node) {
            foreach ($node->getOutNodes() as $outNode) {
                $incomingNodeDb = null;
                $outgoingNodeDb = null;

                foreach ($nodeMap as $_id => $_node) {
                    if ($_node['node'] === $node) {
                        $incomingNodeDb = $_node['db'];
                    } else if ($_node['node'] === $outNode) {
                        $outgoingNodeDb = $_node['db'];
                    }

                    if ($incomingNodeDb !== null && $outgoingNodeDb !== null) {
                        break;
                    }
                }
                /*$idIn = $incomingNodeDb->getId();
                $idOut = $outgoingNodeDb->getId();
                if (false !== $idIn && false !== $idOut) {
                    $connectionsDb = $connectionRepo->findBy(array('incomingNode'=>$idIn, 'outgoingNode'=>$idOut));
                    if (empty($connectionsDb)) {
                        $idIn = false;
                        $idOut = false;
                    }
                }*/
                //if (false === $idIn || false === $idOut) {
                    $connectionDb = new Entity\NodeConnection();
                    $connectionDb->setIncomingNode($incomingNodeDb);
                    $connectionDb->setOutgoingNode($outgoingNodeDb);
                    $this->entityManager->persist($connectionDb);
                //}
            }
        }

        unset($nodeMap);

        $this->entityManager->persist($dbDefinition);
        $this->entityManager->flush();

        $workflow->id = $dbDefinition->getId();

        return $workflow;
        //Disable code
        /*
        NON REIMPLEMENTE
        // Write variable handler rows.
        foreach ($workflow->getVariableHandlers() as $variable => $class)
        {
            $query = $this->db->createInsertQuery();

            $query->insertInto($this->db->quoteIdentifier($this->options['prefix'] . 'variable_handler'))
                  ->set($this->db->quoteIdentifier('workflow_id'), $query->bindValue((int)$workflow->id))
                  ->set($this->db->quoteIdentifier('variable'), $query->bindValue($variable))
                  ->set($this->db->quoteIdentifier('class'), $query->bindValue($class));

            $statement = $query->prepare();
            $statement->execute();
        }

        $this->db->commit();*/
    }

    /**
     * Charge un workflow et le duplique avant de l'enregistrer en base.
     * Il vérifie : Si une autre copie non publié existe => fail
     * La définition parente ne doit pas être archivée
     * @param int $workfowId
     * @throws \Exception si un test ne passe pas
     */
    public function cloneById($workflowId)
    {
        throw new Exception("TODO !!!");
        
    }

    /**
     * Publie la définition du workflow passé en argument et archive la version parente.
     * @param int $workflowId
     * @throws \Exception si déjà publié
     */
    public function publishById($workflowId)
    {
        $repo = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Definition');
        $wfs = $repo->findById($workflowId);

        if (empty($wfs)) {
            throw new \Exception("Unable to load defition id : " . $workflowId);
        }
        $wf = $wfs[0];

        //vérif pas déjà publié
        if (null !== $wf->getPublishedAt()) {
            throw new \Exception("Unable to republish definition");
        }

        $token = $this->security->getToken();
        $user = (is_object($token))? $token->getUsername():'Anonymous';

            
        $wf->setPublishedAt(new \DateTime());
        $wf->setPublishedBy($user);

        $this->entityManager->persist($wf);
        $this->entityManager->flush();

        //Archive le parent si présent
        if (0 === $wf->getParent() || null === $wf->getParent()) {
            return;
        }

        $wfParents = $repo->findById($wf->getParent());
        if (empty($wfParents)) {
            return;
        }
        
        $wfParent = $wfParents[0];

        //Ne fait rien si déjà archivé
        if (null !== $wfParent->getArchivedAt()) {
            return;
        }

        $wfParent->setArchivedAt(new \DateTime());
        $wfParent->setArchivedBy($user);

        $this->entityManager->persist($wfParent);
        $this->entityManager->flush();
    }

    /**
     * Returns the current version number for a given workflow name.
     *
     * @param  string $workflowName
     * @return int
     * 
     * @todo Ajouter la gestion des wf publié ou non
     */
    protected function getCurrentVersionNumber($workflowName)
    {
        $repo = $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Definition');
        $qb = $repo->createQueryBuilder('w');

        $qb->select('MAX(w.version) as version')
            ->where('w.name = :name')
            ->setParameter('name', $workflowName);

        $result = $qb->getQuery()->getResult(\PDO::FETCH_ASSOC);
        
        if ($result !== false && isset($result[0]['version']) && $result[0]['version'] !== null) {
            return $result[0]['version'];
        } else {
            return 0;
        }

    }

    /**
     * Wrapper for serialize() that returns an empty string
     * for empty arrays and null values.
     *
     * @param  mixed $var
     * @return string
     */
    public static function serialize($var)
    {
        $var = serialize($var);

        if ($var == 'a:0:{}' || $var == 'N;') {
            return '';
        }

        return $var;
    }

    /**
     * Wrapper for unserialize().
     *
     * @param  string $serializedVar
     * @param  mixed  $defaultValue
     * @return mixed
     */
    public static function unserialize($serializedVar, $defaultValue = array())
    {
        if (!empty($serializedVar)) {
            return unserialize($serializedVar);
        } else {
            return $defaultValue;
        }
    }

    /**
     * @param DefinitionSearch $param
     * @return DoctrineCollection
     */
    public function getDefinitionForCurrentUser(Entity\DefinitionSearch $param)
    {
        $userRoles = $this->security->getToken()->getUser()->getRoles();
        $param->setRolesForUpdate($userRoles);

        return $this->getQbDefinition($param)->getQuery()->getResult();
    }

    public function getQbDefinition(Entity\DefinitionSearch $param)
    {
        return $this->getRepository()->getQbWithSearch($param);
    }

    private function getRepository()
    {
        return $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Definition');
    }
}
