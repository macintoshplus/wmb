<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowNodeSetExecutionUser extends Units\Test
{
	public function test_init_form()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSetExecutionUser(array());
		$node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());


		$node->activate($mockExecute);

		$this->assert->exception(function () use ($node, $mockExecute) {
			$node->execute($mockExecute);
		})->hasMessage('Unable to use this node if form internal name is not set');

	}

	public function test_init_field()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSetExecutionUser(array('form_internal_name'=>'form1'));
		$node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());


		$node->activate($mockExecute);

		$this->assert->exception(function () use ($node, $mockExecute) {
			$node->execute($mockExecute);
		})->hasMessage('Unable to use this node if field internal name is not set');

	}



	public function test_set_with_string()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSetExecutionUser(array('form_internal_name'=>'form1', 'field_internal_name'=>'user'));
		$node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());

		$user = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowRole();
		$user->setUsername('admin');

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getAnswer = $user;

		$mockExecute->setVariable('form1',array($mock));

		$node->activate($mockExecute);

		$this->assert->boolean($node->execute($mockExecute))->isTrue();

		$this->assert->array($mockExecute->getRoles())->hasSize(1)->contains($user);

	}


	public function test_set_with_array()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSetExecutionUser(array('form_internal_name'=>'form1', 'field_internal_name'=>'user'));
		$node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());

		$user = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowRole();
		$user->setUsername('admin');
		$user2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowRole();
		$user2->setUsername('admin2');

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getAnswer = array($user, $user2);

		$mockExecute->setVariable('form1',array($mock));

		$node->activate($mockExecute);

		$this->assert->exception(function () use ($node, $mockExecute) {
			$node->execute($mockExecute);
		})->hasMessage('Unable to set user on execution');
		

		$this->assert->variable($mockExecute->getRoles())->isNull();

	}
}