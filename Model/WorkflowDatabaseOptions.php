<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Model;


/**
 * Options class for WorkflowDatabase.
 *
 * @property string $prefix
 *                  The database table name prefix to be used.
 *
 */
class WorkflowDatabaseOptions extends BaseOptions
{
    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'prefix' => '',
    );

    /**
     * Property write access.
     *
     * @param string $propertyName  Name of the property.
     * @param mixed  $propertyValue The value for the property.
     *
     * @throws BasePropertyNotFoundException
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set($propertyName, $propertyValue)
    {
        switch ($propertyName) {
            case 'prefix':
                if (!is_string($propertyValue)) {
                    throw new BaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string'
                    );
                }
                break;
            default:
                throw new BasePropertyNotFoundException($propertyName);
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}
