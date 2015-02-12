<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowNodeConditionalFormValue extends Units\Test
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array('form_internal_name'=>'form1', 'field_internal_name'=>'field1'));
		
		$this->assert->variable($node->getInternalName())->isNotNull();
	}

	public function test_conditional_true()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array('form_internal_name'=>'form1', 'field_internal_name'=>'field1'));
		$condition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test');
		$end = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
		$elseEnd = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
        $node->addSelectOutNode($end, $elseEnd, $condition);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());

        $node->activate($mockExecute);

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';
        $mock->getMockController()->getAnswer = 'test';

        $mockExecute->setVariable('form1', array($mock));

        $this->assert->boolean($node->execute($mockExecute))->isTrue();

        $this->mock($end)->call('activate')->once();
	}

	public function test_conditional_false()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array('form_internal_name'=>'form1', 'field_internal_name'=>'field1'));
		$condition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test1');
		$end = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
		$elseEnd = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
        $node->addSelectOutNode($end, $elseEnd, $condition);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());

        $node->activate($mockExecute);

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';
        $mock->getMockController()->getAnswer = 'test';

        $mockExecute->setVariable('form1', array($mock));

        $this->assert->boolean($node->execute($mockExecute))->isTrue();

        $this->mock($elseEnd)->call('activate')->once();
	}
}