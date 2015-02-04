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
	 * @var Container container
	 */
	private $container;

	private $mailer;

	private $twig;

	/**
	 * @param Container $container
	 */
	//public function __construct(Container $container)
	public function __construct(Swift_Mailer $mailer, Twig_Environment $twig)
	{
		//$this->container = $container;
		$this->twig = $twig;
		$this->mailer = $mailer;
	}

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

		//Injection de dépendance aux besoins
		switch ($type) {
			case 'Email':
				$node->setTwig($this->twig);
				$node->setMailer($this->mailer);
				return $node;
			case 'ComputeExecutionName':
				$node->setTwig($this->twig);
				return $node;
		}

		return $node;
	}

} // END class WorkflowNodeFactory 