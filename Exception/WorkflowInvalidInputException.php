<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Exception;

/**
 * This exception will be thrown when an error occurs
 * during input validation in an input node.
 *
 * @property-read array $errors The input validation error(s).
 *
 */
class WorkflowInvalidInputException extends WorkflowExecutionException
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'errors' => array(),
    );

    /**
     * Constructor.
     *
     * @param array $message
     */
    public function __construct( $message )
    {
        $this->properties['errors'] = $message;

        $messages = array();

        foreach ( $message as $variable => $condition )
        {
            $messages[] = $variable . ' ' . $condition;
        }

        parent::__construct( join( "\n", $messages ) );
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
    public function __get( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'errors':
                return $this->properties[$propertyName];
        }

        throw new BasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws BasePropertyPermissionException
     *         If there is a write access to errors.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName )
        {
            case 'errors':
                throw new BasePropertyPermissionException( $propertyName, BasePropertyPermissionException::WRITE );
        }

        throw new BasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'errors':
                return true;
        }

        return false;
    }
}

