<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Exception;

/**
 * BasePropertyNotFoundException is thrown whenever a non existent property
 * is accessed in the Components library.
 *
 */
class BasePropertyNotFoundException extends BaseException
{
    /**
     * Constructs a new BasePropertyNotFoundException for the property
     * $name.
     *
     * @param string $name The name of the property
     */
    function __construct( $name )
    {
        parent::__construct( "No such property name '{$name}'." );
    }
}

