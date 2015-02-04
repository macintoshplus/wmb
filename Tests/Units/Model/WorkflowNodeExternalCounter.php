<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowNodeExternalCounter extends Units\Test
{
	public function test_init()
	{
		$controller = new \atoum\mock\controller();
		$controller->__construct = function() {};

		$entityManager = new Mock\Doctrine\ORM\EntityManagerInterface();
		//$meta = new Mock\Doctrine\ORM\Mapping\ClassMetadata();
		//$repo = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Repository\ExecutionRepository(null, $meta);
		//$repo->getMockController()->getExecutionById = array();
		//$entityManager->getMockController()->getRepository = $repo;

		$definitionService = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageInterface();

		$security = new Mock\Symfony\Component\Security\Core\SecurityContextInterface();

		$mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, null, $controller);
		$mockExecute->getMockController()->getId = 1;

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeExternalCounter(array('counter_name'=>'dde'));
		$node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());


		$node->activate($mockExecute);

		$this->assert->exception(function () use ($node, $mockExecute) {
			$node->execute($mockExecute);
		})->hasMessage('Unable to use this node if counter service is not set');

		$counter = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowExternalCounterInterface();
		$counter->getMockController()->getNext = 1;
		$node->setExternalCounter($counter);

		$this->assert->boolean($node->execute($mockExecute))->isTrue();

		$this->assert->boolean($mockExecute->hasVariable('counter'))->isTrue();
		$this->assert->integer($mockExecute->getVariable('counter'))->isEqualTo(1);

	}
}