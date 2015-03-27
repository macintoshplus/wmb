<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorNodeCollector;
use JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitor;
use JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyNotFoundException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyPermissionException;

use Countable;

/**
 * Class representing a workflow.
 *
 * @property WorkflowDefinitonStorage $definitionStorage
 *           The definition handler used to fetch sub workflows on demand.
 *           This property is set automatically if you load a workflow using
 *           a workflow definition storage.
 * @property int                         $id
 *           Unique ID set automatically by the definition handler when the
 *           workflow is stored.
 * @property string                      $name
 *           A unique name (accross the system) for this workflow.
 * @property int                         $version
 *           The version of the workflow. This must be incremented manually
 *           whenever you want a new version.
 * @property-read WorkflowNodeStart   $startNode The unique start node of the workflow.
 * @property-read WorkflowNodeEnd     $endNode The default end node of the workflow.
 * @property-read WorkflowNodeFinally $finallyNode The start of a node
 *                                       sequence that is executed when a
 *                                       workflow execution is cancelled.
 * @property-read array(WorkflowNode) $nodes All the nodes of this workflow.
 *
 * @mainclass
 */
class Workflow implements Countable, WorkflowVisitableInterface
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'definitionStorage' => null,
      'id'                => false,
      'name'              => '',
      'startNode'         => null,
      'endNode'           => null,
      'finallyNode'       => null,
      'version'           => 1
    );

    protected $rolesForUpdate = null;

    protected $rolesForUse = null;

    protected $archivedAt = null;

    protected $publishedAt = null;

    protected $parent = null;


    /**
     * The variable handlers of this workflow.
     *
     * @var array
     */
    protected $variableHandlers = array();

    /**
     * Constructs a new workflow object with the name $name.
     *
     * Use $startNode and $endNode parameters if you don't want to use the
     * default start and end nodes.
     *
     * $name must uniquely identify the workflow within the system.
     *
     * @param string                 $name        The name of the workflow.
     * @param WorkflowNodeStart   $startNode   The start node of the workflow.
     * @param WorkflowNodeEnd     $endNode     The default end node of the workflow.
     * @param WorkflowNodeFinally $finallyNode The start of a node sequence
     *                                            that is executed when a workflow
     *                                            execution is cancelled.
     */
    public function __construct($name, WorkflowNodeStart $startNode = null, WorkflowNodeEnd $endNode = null, WorkflowNodeFinally $finallyNode = null)
    {
        $this->name = $name;

        // Create a new WorkflowNodeStart object, if necessary.
        if (null === $startNode) {
            $this->properties['startNode'] = new WorkflowNodeStart;
        } else {
            $this->properties['startNode'] = $startNode;
        }

        // Create a new WorkflowNodeEnd object, if necessary.
        if (null === $endNode) {
            $this->properties['endNode'] = new WorkflowNodeEnd;
        } else {
            $this->properties['endNode'] = $endNode;
        }

        // Create a new WorkflowNodeFinally object, if necessary.
        if (null === $finallyNode) {
            $this->properties['finallyNode'] = new WorkflowNodeFinally;
        } else {
            $this->properties['finallyNode'] = $finallyNode;
        }
    }

    /**
     * Property read access.
     *
     * @throws BasePropertyNotFoundException 
     *         If the the desired property is not found.
     * 
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get($propertyName)
    {
        switch ($propertyName) {
            case 'definitionStorage':
            case 'id':
            case 'name':
            case 'startNode':
            case 'endNode':
            case 'finallyNode':
            case 'version':
                return $this->properties[$propertyName];

            case 'nodes':
                $visitor = new WorkflowVisitorNodeCollector($this);

                return $visitor->getNodes();
        }

        throw new BasePropertyNotFoundException($propertyName);
    }

    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws BaseValueException 
     *         If the value for the property definitionStorage is not an
     *         instance of WorkflowDefinitionStorageInterface.
     * @throws BaseValueException 
     *         If the value for the property id is not an integer.
     * @throws BaseValueException 
     *         If the value for the property name is not a string.
     * @throws BasePropertyPermissionException 
     *         If there is a write access to startNode.
     * @throws BasePropertyPermissionException 
     *         If there is a write access to endNode.
     * @throws BasePropertyPermissionException 
     *         If there is a write access to finallyNode.
     * @throws BasePropertyPermissionException 
     *         If there is a write access to nodes.
     * @throws BaseValueException 
     *         If the value for the property version is not an integer.
     * @ignore
     */
    public function __set($propertyName, $val)
    {
        switch ($propertyName) {
            case 'definitionStorage':
                if (!($val instanceof WorkflowDefinitionStorageInterface)) {
                    throw new BaseValueException($propertyName, $val, 'WorkflowDefinitionStorageInterface');
                }

                $this->properties['definitionStorage'] = $val;

                return;

            case 'id':
                if (!(is_int($val)) && false !== $val) {
                    throw new BaseValueException($propertyName, $val, 'integer');
                }

                $this->properties['id'] = $val;

                return;

            case 'name':
                if (!(is_string($val))) {
                    throw new BaseValueException($propertyName, $val, 'string');
                }

                $this->properties['name'] = $val;

                return;

            case 'startNode':
            case 'endNode':
            case 'finallyNode':
            case 'nodes':
                throw new BasePropertyPermissionException($propertyName, BasePropertyPermissionException::READ);

            case 'version':
                if (!is_int($val)) {
                    throw new BaseValueException($propertyName, $val, 'integer');
                }

                $this->properties['version'] = $val;

                return;
        }

        throw new BasePropertyNotFoundException($propertyName);
    }
 
    /**
     * Property isset access.
     * 
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset($propertyName)
    {
        switch ($propertyName)
        {
            case 'definitionStorage':
            case 'id':
            case 'name':
            case 'startNode':
            case 'endNode':
            case 'finallyNode':
            case 'nodes':
            case 'version':
                return true;
        }

        return false;
    }

    /**
     * Set rolesForUpdate
     *
     * @param array $rolesForUpdate
     * @return Workflow
     */
    public function setRolesForUpdate($rolesForUpdate)
    {
        $this->rolesForUpdate = $rolesForUpdate;

        return $this;
    }

    /**
     * Get rolesForUpdate
     *
     * @return array 
     */
    public function getRolesForUpdate()
    {
        return $this->rolesForUpdate;
    }

    /**
     * Set rolesForUse
     *
     * @param array $rolesForUse
     * @return Workflow
     */
    public function setRolesForUse($rolesForUse)
    {
        $this->rolesForUse = $rolesForUse;

        return $this;
    }

    /**
     * Get rolesForUse
     *
     * @return array 
     */
    public function getRolesForUse()
    {
        return $this->rolesForUse;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return Workflow
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime 
     */
    public function isPublished()
    {
        return (null !== $this->publishedAt);
    }

    /**
     * Set archivedAt
     *
     * @param \DateTime $archivedAt
     * @return Workflow
     */
    public function setArchivedAt($archivedAt)
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    /**
     * Get archivedAt
     *
     * @return \DateTime 
     */
    public function isArchived()
    {
        return (null !== $this->archivedAt);
    }

    /**
     * Set parent
     *
     * @param integer $parent
     * @return Workflow
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return integer 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the number of nodes of this workflow.
     *
     * @return integer
     */
    public function count()
    {
        $visitor = new WorkflowVisitor;
        $this->accept($visitor);

        return count($visitor);
    }

    /**
     * Returns true when the workflow requires user interaction
     * (ie. when it contains WorkflowNodeInput nodes)
     * and false otherwise.
     *
     * @return boolean true when the workflow is interactive, false otherwise.
     */
    public function isInteractive()
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof WorkflowNodeInput || $node instanceof WorkflowNodeForm) {
                return true;
            }
        }

        return false;
    }

    /**
     * return the list of Form name and code
     * @return array
     */
    public function getFormType()
    {
        $list = array();
        foreach ($this->nodes as $node) {
            if ($node instanceof WorkflowNodeForm) {
                $name = $node->getName();
                $list[$node->getInternalName()] = (null !== $name)? $name:'Form '.$node->getInternalName();
            }
        }
        return $list;
    }

    public function getFormFieldRequired()
    {
        $list = $this->getFormType();
        $fields = array();
        foreach ($this->nodes as $node) {
            if ($node instanceof WorkflowNodeFormFieldAccessInterface) {
                $form = $node->getFormInternalName();
                //Pas utilisé, il ajoute
                if (!array_key_exists($form, $fields)) {
                    $fields[$form] = array('name'=>$list[$form], 'fields'=>array());
                }
                //Ajout des infos sur le chams
                $fieldsKey = $node->getFieldInternalName();
                $infos = array();
                $infos['node_id'] = $node->getId();
                $infos['node_name'] = $node->getName();
                $infos['node_class'] = get_class($node);
                $fields[$form]['fields'][$fieldsKey][] = $infos;
            }
        }
        return $fields;
    }

    /**
     * @return array
     */
    public function getEmailParameters()
    {
        $list = array();
        foreach ($this->nodes as $node) {
            if ($node instanceof WorkflowNodeEmail) {
                $config = array('name' => $node->getName());
                $config['to'] = $node->getTo();
                $config['from'] = $node->getFrom();
                $config['subject'] = $node->getSubject();
                $config['body'] = $node->getBody();
                $list[$node->getId()] = $config;
            }
        }
        return $list;
    }

    /**
     * @param integer $nodeid
     * @param array   $param
     */
    public function setEmailParameters($nodeid, array $param)
    {
        if (null !== $this->publishedAt) {
            throw new \Exception("Unable to alter published definition");
        }
        //Si le tableau de paramètre n'a pas toutes les clées
        /*if (4 !== count(array_intersect_key($param, array('to'=>null, 'from'=>null, 'body'=>null, 'subject'=>null)))) {
            throw new \Exception("Invalid param values !");
        }*/
        if (!is_integer($nodeid)) {
            $nodeid = intval($nodeid);
        }

        $nodes = $this->nodes;
        foreach ($nodes as $node) {
            if ($node instanceof WorkflowNodeEmail && $node->getId() === $nodeid) {
                $arr = $node->getConfiguration();
                $arr['name'] = $node->getName();
                $params = array_merge($arr, $param);

                $node->setName($params['name']);
                $node->setFrom($params['from']);
                $node->setTo($params['to']);
                $node->setSubject($params['subject']);
                $node->setBody($params['body']);

                return;
            }
        }

        throw new \Exception("Unable to set email parameters for node id ".$nodeid." (node not found or not type Email)");
    }
    /**
     * @return array
     */
    public function getDateParameters()
    {
        $list = array();
        foreach ($this->nodes as $node) {
            if ($node instanceof WorkflowNodeControlForm) {
                $config = array('name' => $node->getName());
                $config['date'] = $node->getOutDate();
                $list[$node->getId()] = $config;
            }
        }
        return $list;
    }

    /**
     * @param integer  $nodeid
     * @param DateTime $param
     */
    public function setDateParameters($nodeid, \DateTime $date)
    {
        if (null !== $this->publishedAt) {
            throw new \Exception("Unable to alter published definition");
        }

        if (!is_integer($nodeid)) {
            $nodeid = intval($nodeid);
        }

        $nodes = $this->nodes;
        foreach ($nodes as $node) {
            if ($node instanceof WorkflowNodeControlForm && $node->getId() === $nodeid) {
                $node->setOutDate($date);

                return;
            }
        }

        throw new \Exception("Unable to set date parameters for node id ".$nodeid." (node not found or not type ControlForm)");
    }

    /**
     * Returns true when the workflow has sub workflows
     * (ie. when it contains WorkflowNodeSubWorkflow nodes)
     * and false otherwise.
     *
     * @return boolean true when the workflow has sub workflows, false otherwise.
     */
    public function hasSubWorkflows()
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof WorkflowNodeSubWorkflow) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resets the nodes of this workflow.
     *
     * See the documentation of WorkflowVisitorReset for
     * details.
     */
    public function reset()
    {
        $this->accept(new WorkflowVisitorReset);
    }

    /**
     * Verifies the specification of this workflow.
     *
     * See the documentation of WorkflowVisitorVerification for
     * details.
     *
     * @throws WorkflowInvalidWorkflowException if the specification of this workflow is not correct.
     */
    public function verify()
    {
        $this->accept(new WorkflowVisitorVerification);
    }

    /**
     * Overridden implementation of accept() calls
     * accept on the start node.
     *
     * @param WorkflowVisitor $visitor
     */
    public function accept(WorkflowVisitor $visitor)
    {
        $visitor->visit($this);
        $this->properties['startNode']->accept($visitor);
    }

    /**
     * Sets the class $className to handle the variable named $variableName.
     *
     * $className must be the name of a class implementing the
     * WorkflowVariableHandler interface.
     *
     * @param string $variableName
     * @param string $className
     * @throws WorkflowInvalidWorkflowException if $className does not contain the name of a valid class implementing WorkflowVariableHandler
     */
    public function addVariableHandler($variableName, $className)
    {
        if (class_exists($className)) {
            $class = new ReflectionClass($className);

            if ($class->implementsInterface('WorkflowVariableHandler')) {
                $this->variableHandlers[$variableName] = $className;
            } else {
                throw new WorkflowInvalidWorkflowException(
                    sprintf('Class "%s" does not implement the WorkflowVariableHandler interface.', $className)
                );
            }
        } else {
            throw new WorkflowInvalidWorkflowException(
                sprintf('Class "%s" not found.', $className)
            );
        }
    }

    /**
     * Removes the handler for $variableName and returns true
     * on success.
     *
     * Returns false if no handler was set for $variableName.
     *
     * @param string $variableName
     * @return boolean
     */
    public function removeVariableHandler($variableName)
    {
        if (isset($this->variableHandlers[$variableName])) {
            unset($this->variableHandlers[$variableName]);
            return true;
        }

        return false;
    }

    /**
     * Sets handlers for multiple variables.
     *
     * The format of $variableHandlers is
     * array('variableName' => WorkflowVariableHandler)
     *
     * @throws WorkflowInvalidWorkflowException if $className does not contain the name of a valid class implementing WorkflowVariableHandler
     * @param array $variableHandlers
     */
    public function setVariableHandlers(array $variableHandlers)
    {
        $this->variableHandlers = array();

        foreach ($variableHandlers as $variableName => $className) {
            $this->addVariableHandler($variableName, $className);
        }
    }

    /**
     * Returns the variable handlers.
     *
     * The format of the returned array is
     * array('variableName' => WorkflowVariableHandler)
     *
     * @return array
     */
    public function getVariableHandlers()
    {
        return $this->variableHandlers;
    }
}
