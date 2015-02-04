<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Twig_Environment;

/**
 * WorkflowNodeComputeExecutionName class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeComputeExecutionName extends WorkflowNode
{

	private $twig;

    /**
     * @param array $configuration
     */
	public function __construct( array $configuration = null )
    {

        if ( !isset( $configuration['template'] ) )
        {
            $configuration['template'] = "Execution name {{execution_id}}";
        }

        parent::__construct( $configuration );
    }

    public function execute( WorkflowExecution $execution )
    {

        if ( !isset( $this->twig ) ){
        	throw new \Exception("Enable to use this node if twig service is not set");
        }

        $array = $execution->getVariables();
        $array['execution_id'] = $execution->getId();
        $array['workflow_name'] = $execution->workflow->name;
        $array['workflow_id'] = $execution->workflow->id;

        $name = $this->twig->render($this->configuration['template'], $array);

        $execution->setName($name);

		$this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );

    }

    /**
     * defini le service de rendu des templates
     * @param Twig $twig;
     */
    public function setTwig(Twig_Environment $twig)
    {
    	$this->twig = $twig;
    }

} // END class WorkflowNodeEmail 