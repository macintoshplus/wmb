<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use DOMElement;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseAutoloadException;

/**
 * An object of the WorkflowNodeAction class represents an activity node holding business logic.
 *
 * When the node is reached during execution of the workflow, the business logic that is implemented
 * by the associated service object is executed.
 *
 * Service objects can return true to resume execution of the
 * workflow or false to suspend the workflow (unless there are other active nodes)
 * and be re-executed later
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * The following example displays how to create a workflow with a very
 * simple service object that prints the argument it was given to the
 * constructor:
 *
 * <code>
 * <?php
 * class MyPrintAction implements WorkflowServiceObject
 * {
 *     private $whatToSay;
 *
 *     public function __construct( $whatToSay )
 *     {
 *         $this->whatToSay = $whatToSay;
 *     }
 *
 *     public function execute( WorkflowExecution $execution )
 *     {
 *         print $this->whatToSay;
 *         return true; // we're finished, activate next node
 *     }
 *
 *     public function __toString()
 *     {
 *         return 'action description';
 *     }
 * }
 *
 * $workflow = new Workflow( 'Test' );
 *
 * $action = new WorkflowNodeAction( array( "class" => "MyPrintAction",
 *                                             "arguments" => "No. 1 The larch!" ) );
 * $action->addOutNode( $workflow->endNode );
 * $workflow->startNode->addOutNode( $action );
 * ?>
 * </code>
 *
 */
class WorkflowNodeAction extends WorkflowNode
{
    /**
     * Constructs a new action node with the configuration $configuration.
     *
     * Configuration format
     * <ul>
     * <li>
     *   <b>String:</b>
     *   The class name of the service object. Must implement WorkflowServiceObject. No
     *   arguments are passed to the constructor.
     * </li>
     *
     * <li>
     *   <b>Array:</b>
     *   <ul>
     *     <li><i>class:</i> The class name of the service object. Must implement WorkflowServiceObject.</li>
     *     <li><i>arguments:</i> Array of values that are passed to the constructor of the service object.</li>
     *   </ul>
     * <li>
     * </ul>
     *
     * @param mixed $configuration
     * @throws WorkflowDefinitionStorageInterfaceException
     */
    public function __construct( $configuration )
    {
        if ( is_string( $configuration ) )
        {
            $configuration = array( 'class' => $configuration );
        }

        if ( !isset( $configuration['arguments'] ) )
        {
            $configuration['arguments'] = array();
        }

        parent::__construct( $configuration );
    }

    /**
     * Executes this node by creating the service object and calling its execute() method.
     *
     * If the service object returns true, the output node will be activated.
     * If the service node returns false the workflow will be suspended
     * unless there are other activated nodes. An action node suspended this way
     * will be executed again the next time the workflow is resumed.
     *
     * @param WorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( WorkflowExecution $execution )
    {
        $object   = $this->createObject();
        $finished = $object->execute( $execution );

        // Execution of the Service Object has finished.
        if ( $finished !== false )
        {
            $this->activateNode( $execution, $this->outNodes[0] );

            return parent::execute( $execution );
        }
        // Execution of the Service Object has not finished.
        else
        {
            return false;
        }
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML( \DOMElement $element )
    {
        $configuration = array(
          'class'     => $element->getAttribute( 'serviceObjectClass' ),
          'arguments' => array()
        );

        $childNode = WorkflowDefinitionStorageInterfaceXml::getChildNode( $element );

        if ( $childNode->tagName == 'arguments' )
        {
            foreach ( WorkflowDefinitionStorageInterfaceXml::getChildNodes( $childNode ) as $argument )
            {
                $configuration['arguments'][] = WorkflowDefinitionStorageInterfaceXml::xmlToVariable( $argument );
            }
        }

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML( \DOMElement $element )
    {
        $element->setAttribute( 'serviceObjectClass', $this->configuration['class'] );

        if ( !empty( $this->configuration['arguments'] ) )
        {
            $xmlArguments = $element->appendChild(
              $element->ownerDocument->createElement( 'arguments' )
            );

            foreach ( $this->configuration['arguments'] as $argument )
            {
                $xmlArguments->appendChild(
                  WorkflowDefinitionStorageInterfaceXml::variableToXml(
                    $argument, $element->ownerDocument
                  )
                );
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
        try
        {
            $buffer = (string)$this->createObject();
        }
        catch ( BaseAutoloadException $e )
        {
            return 'Class not found.';
        }
        catch ( WorkflowExecutionException $e )
        {
            return $e->getMessage();
        }

        return $buffer;
    }

    /**
     * Returns the service object as specified by the configuration.
     *
     * @return WorkflowServiceObject
     */
    protected function createObject()
    {
        if ( !class_exists( $this->configuration['class'] ) )
        {
            throw new WorkflowExecutionException(
              sprintf(
                'Class "%s" not found.',
                $this->configuration['class']
              )
            );
        }

        $class = new \ReflectionClass( $this->configuration['class'] );

        if ( !$class->implementsInterface( 'WorkflowServiceObject' ) )
        {
            throw new WorkflowExecutionException(
              sprintf(
                'Class "%s" does not implement the WorkflowServiceObject interface.',
                $this->configuration['class']
              )
            );
        }

        if ( !empty( $this->configuration['arguments'] ) )
        {
            return $class->newInstanceArgs( $this->configuration['arguments'] );
        }
        else
        {
            return $class->newInstance();
        }
    }
}

