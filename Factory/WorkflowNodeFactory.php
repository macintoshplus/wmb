<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Factory;

use JbNahan\Bundle\WorkflowManagerBundle\Model;
use Symfony\Component\DependencyInjection\Container;
use \Twig_Environment;
use \Swift_Mailer;

/**
 * WorkflowNodeFactory class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeFactory
{

    /**
     * Make a new node for workflow
     * @param string $type
     * @param array  $configuration
     * @return object
     */
    public function createNode($type, array $configuration = null)
    {
        $classname = 'JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNode' . $type;
        if (!class_exists($classname)) {
            throw new \Exception("Class " . $classname . " does not exists !");
        }

        //Génération de la node
        $node = new $classname($configuration);

        return $node;
    }
}
