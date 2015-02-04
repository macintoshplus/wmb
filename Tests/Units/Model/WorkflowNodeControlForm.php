<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowNodeControlForm extends Units\Test
{
	public function test_execute()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeControlForm(array('internal_name'=>'form_1','out_date'=>new \DateTime('2015-02-20')));
		

		$trueNode  = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintTrue' );
		$falseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintFalse' );

		$node->addSelectOutNode($trueNode, $falseNode);

		$node->activate($mockExecute);

		$this->assert->boolean($node->execute($mockExecute))->isFalse();

		$mockExecute->setVariable('form_1',array(array('data'=>'toto')));

		$this->assert->boolean($node->execute($mockExecute))->isFalse();

		$this->assert->array($mockExecute->getActivatedNodes())->hasSize(1);
		//var_dump($mockExecute->getActivatedNodes());

	}


	public function test_execute_date_pass_no_answers()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeControlForm(array('internal_name'=>'form_1','out_date'=>new \DateTime('2015-02-01')));
		

		$continueNode  = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintContinue' );
		$elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintElse' );

		$node->addSelectOutNode($continueNode, $elseNode);

		$node->activate($mockExecute);

		$this->assert->boolean($node->execute($mockExecute))->isTrue();
		//var_dump($mockExecute->getActivatedNodes());

		$this->assert->array($mockExecute->getActivatedNodes())->hasSize(2);

	}

	public function test_execute_date_pass_with_answers()
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

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeControlForm(array('internal_name'=>'form_1','out_date'=>new \DateTime('2015-02-01')));
		

		$continueNode  = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintContinue' );
		$elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintElse' );

		$node->addSelectOutNode($continueNode, $elseNode);

		$node->activate($mockExecute);

		$mockExecute->setVariable('form_1', array(array('data'=>'toto')));

		$this->assert->boolean($node->execute($mockExecute))->isTrue();
		//var_dump($mockExecute->getActivatedNodes());
		$this->assert->array($mockExecute->getActivatedNodes())->hasSize(2);

	}
}