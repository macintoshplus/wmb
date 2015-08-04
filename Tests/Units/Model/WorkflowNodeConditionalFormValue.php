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

		$definitionService = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageInterface();

		$security = new Mock\Symfony\Component\Security\Core\SecurityContextInterface();
        $mockLogger = new Mock\Psr\Log\LoggerInterface();
        $controllerSwift = new \atoum\mock\controller();
        $controllerSwift->__construct = function () {};
        $mockSwift = new Mock\Swift_Mailer(new Mock\Swift_Transport(), $controllerSwift);
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

		$mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
		$mockExecute->getMockController()->getId = 1;

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array('form_internal_name'=>'form1', 'field_internal_name'=>'field1'));
		
		$this->assert->variable($node->getInternalName())->isNotNull();
	}

	public function test_conditional_true()
	{
		$controller = new \atoum\mock\controller();
		$controller->__construct = function() {};

		$entityManager = new Mock\Doctrine\ORM\EntityManagerInterface();

		$definitionService = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageInterface();

		$security = new Mock\Symfony\Component\Security\Core\SecurityContextInterface();
        $mockLogger = new Mock\Psr\Log\LoggerInterface();
        $controllerSwift = new \atoum\mock\controller();
        $controllerSwift->__construct = function () {};
        $mockSwift = new Mock\Swift_Mailer(new Mock\Swift_Transport(), $controllerSwift);
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

		$mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
		$mockExecute->getMockController()->getId = 1;

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array('form_internal_name'=>'form1', 'field_internal_name'=>'field1'));
		$condition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test');
		$end = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
		$elseEnd = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
        $node->addSelectOutNode($end, $elseEnd, $condition);
        //$node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());

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

		$definitionService = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageInterface();

		$security = new Mock\Symfony\Component\Security\Core\SecurityContextInterface();
        $mockLogger = new Mock\Psr\Log\LoggerInterface();
        $controllerSwift = new \atoum\mock\controller();
        $controllerSwift->__construct = function () {};
        $mockSwift = new Mock\Swift_Mailer(new Mock\Swift_Transport(), $controllerSwift);
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

		$mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
		$mockExecute->getMockController()->getId = 1;

		$node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array('form_internal_name'=>'form1', 'field_internal_name'=>'field1'));
		$condition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test1');
		$end = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
		$elseEnd = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
        $node->addSelectOutNode($end, $elseEnd, $condition);
        //$node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());

        $node->activate($mockExecute);

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';
        $mock->getMockController()->getAnswer = 'test';

        $mockExecute->setVariable('form1', array($mock));

        $this->assert->boolean($node->execute($mockExecute))->isTrue();

        $this->mock($elseEnd)->call('activate')->once();
	}

    public function test_export_xml()
    {
        $storage = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageXml();
        $def = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $def->definitionStorage = $storage;
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue([
        'internal_name'=>'condition1',
        'form_internal_name'=>'form1',
        'field_internal_name'=>'field_3_14']);
        $def->startNode->addOutNode($node);
        $condition = new \JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test1');

        $node->addSelectOutNode($def->endNode, $def->finallyNode, $condition);

        $element = $storage->saveToDocument($def, 1);

        $this->assert->string($element->saveXML())->contains('condition1')->contains('test1')->contains('form1')->contains('field_3_14');
        
        $document = new \DOMDocument('1.0', 'UTF-8');
        $nodeXml = $document->createElement('node');
        $node->configurationToXML($nodeXml);

        $config = \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue::configurationFromXML($nodeXml);

        $this->assert->array($config)->hasSize(3)->containsValues(['condition1', 'form1', 'field_3_14']);
    }
}