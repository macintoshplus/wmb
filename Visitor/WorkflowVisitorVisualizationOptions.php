<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Visitor;

use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Model\BaseOptions;

/**
 * Options class for WorkflowVisitorVisualization.
 *
 * @property string $colorHighlighted
 *           The color for highlighted nodes.
 * @property string $colorNormal
 *           The normal color for nodes.
 * @property array  $highlightedNodes
 *           The array of nodes that are to be highlighted.
 * @property array  $workflowVariables
 *           The workflow variables that are to be displayed.
 */
class WorkflowVisitorVisualizationOptions extends BaseOptions
{
    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'colorHighlighted'  => '#cc0000',
        'colorNormal'       => '#2e3436',
        'highlightedNodes'  => array(),
        'workflowVariables' => array(),
        'optionsByClass'    => array()
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
        switch ($propertyName)
        {
            case 'colorHighlighted':
            case 'colorNormal':
                if (!is_string($propertyValue)) {
                    throw new BaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string'
                    );
                }
                break;
            case 'highlightedNodes':
            case 'workflowVariables':
            case 'optionsByClass':
                if (!is_array($propertyValue)) {
                    throw new BaseValueException(
                        $propertyName,
                        $propertyValue,
                        'array'
                    );
                }
                break;
            default:
                throw new BasePropertyNotFoundException($propertyName);
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}
