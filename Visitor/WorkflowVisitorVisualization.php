<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Visitor;

use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowVisitableInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyNotFoundException;
use JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNode;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalBranch;

/**
 * An implementation of the WorkflowVisitor interface that
 * generates GraphViz/dot markup for a workflow definition.
 *
 * <code>
 * <?php
 * $visitor = new WorkflowVisitorVisualization;
 * $workflow->accept($visitor);
 * print $visitor;
 * ?>
 * </code>
 *
 * @property WorkflowVisitorVisualizationOptions $options
 *
 */
class WorkflowVisitorVisualization extends WorkflowVisitor
{
    /**
     * Holds the displayed strings for each of the nodes.
     *
     * @var array(string => string)
     */
    protected $nodes = array();

    /**
     * Holds all the edges of the graph.
     *
     * @var array(id => array(WorkflowNode))
     */
    protected $edges = array();

    /**
     * Holds the name of the workflow.
     *
     * @var string
     */
    protected $workflowName = 'Workflow';

    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->options = new WorkflowVisitorVisualizationOptions;
    }

    /**
     * Property get access.
     *
     * @throws BasePropertyNotFoundException
     *         If the given property could not be found.
     * @param string $propertyName
     * @ignore
     */
    public function __get($propertyName)
    {
        if ($this->__isset($propertyName)) {
            return $this->properties[$propertyName];
        }
        throw new BasePropertyNotFoundException($propertyName);
    }

    /**
     * Property set access.
     *
     * @throws BasePropertyNotFoundException
     * @param string $propertyName
     * @param string $propertyValue
     * @ignore
     */
    public function __set($propertyName, $propertyValue)
    {
        switch ($propertyName) {
            case 'options':
                if (!($propertyValue instanceof WorkflowVisitorVisualizationOptions)) {
                    throw new BaseValueException(
                        $propertyName,
                        $propertyValue,
                        'WorkflowVisitorVisualizationOptions'
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
        return array_key_exists($propertyName, $this->properties);
    }

    /**
     * Perform the visit.
     *
     * @param WorkflowVisitableInterface $visitable
     */
    protected function doVisit(WorkflowVisitableInterface $visitable)
    {
        if ($visitable instanceof Workflow) {
            $this->workflowName = $visitable->name;

            // The following line of code is not a no-op. It triggers the
            // Workflow::__get() method, thus initializing the respective
            // WorkflowVisitorNodeCollector object.
            $visitable->nodes;
        }

        if ($visitable instanceof WorkflowNode) {
            $id = $visitable->getId();

            $className = get_class($visitable);

            $defaultOptions = array();

            if (array_key_exists($className, $this->options['optionsByClass'])) {
                $defaultOptions = $this->options['optionsByClass'][$className];
            }
            
            // set defaulf color if not set
            if (!array_key_exists('color', $defaultOptions)) {
                $defaultOptions['color'] = $this->options['colorNormal'];
            }

            // hightlight node
            if (in_array($id, $this->options['highlightedNodes'])) {
                $defaultOptions['color'] = $this->options['colorHighlighted'];
            }

            if (!isset($this->nodes[$id])) {
                $defaultOptions['label'] = (string)$visitable;
                $this->nodes[$id] = $defaultOptions;
                /*array(
                  'label' => (string)$visitable,
                  'color' => $color
                );*/
            }

            $outNodes = array();

            foreach ($visitable->getOutNodes() as $outNode) {
                $label = '';

                if ($visitable instanceof WorkflowNodeConditionalBranch) {
                    $condition = $visitable->getCondition($outNode);

                    if ($condition !== false) {
                        $label = ' [label="' . $condition . '"]';
                    }
                }

                $outNodes[] = array($outNode->getId(), $label);
            }

            $this->edges[$id] = $outNodes;
        }
    }

    /**
     * Returns a the contents of a graphviz .dot file.
     *
     * @return boolean
     * @ignore
     */
    public function __toString()
    {
        $dot = 'digraph "' . $this->workflowName . "\" {\n";

        foreach ($this->nodes as $key => $data) {
            $opt = array();
            foreach ($data as $keyData => $value) {
                $opt[] = sprintf('%s="%s"', $keyData, (string)$value);
            }
            $dot .= sprintf(
                "node%s [%s]\n",
                $key,
                implode(', ', $opt)
            );
        }

        $dot .= "\n";

        foreach ($this->edges as $fromNode => $toNodes) {
            foreach ($toNodes as $toNode) {
                $dot .= sprintf(
                    "node%s -> node%s%s\n",
                    $fromNode,
                    $toNode[0],
                    $toNode[1]
                );
            }
        }

        if (!empty($this->options['workflowVariables'])) {
            $dot .= 'variables [shape=none, label=<<table>';

            foreach ($this->options['workflowVariables'] as $name => $value) {
                $dot .= sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    $name,
                    htmlspecialchars(WorkflowUtil::variableToString($value))
                );
            }

            $dot .= "</table>>]\n";
        }

        return $dot . "}\n";
    }
}
