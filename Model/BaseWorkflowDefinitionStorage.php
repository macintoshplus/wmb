<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowDefinitionStorageInterfaceException;
use JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionInterface;

/**
 * Base workflow definition storage handler.
 */
abstract class BaseWorkflowDefinitionStorage implements WorkflowDefinitionStorageInterface
{



    /**
     * Returns the default configuration for a node class.
     *
     * @param string $className
     * @return mixed
     */
    public static function getDefaultConfiguration( $className )
    {
        $configuration = null;

        $class    = new \ReflectionClass( $className );
        $defaults = $class->getDefaultProperties();

        if ( isset( $defaults['configuration'] ) )
        {
            $configuration = $defaults['configuration'];
        }

        return $configuration;
    }
}

