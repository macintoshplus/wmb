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
        $mockLogger = new Mock\Psr\Log\LoggerInterface();
        $controllerSwift = new \atoum\mock\controller();
        $controllerSwift->__construct = function () {};
        $mockSwift = new Mock\Swift_Mailer(new Mock\Swift_Transport(), $controllerSwift);
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, $controller);
        $mockExecute->getMockController()->getId = 1;

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeExternalCounter(array('var_name'=>'counter', 'counter_name'=>'dde'));
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

    public function test_verify()
    {
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeExternalCounter();
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->addInNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart());

        $this->assert->exception(function () use ($node) {
            $node->verify();
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
        ->hasMessage('Node external counter has no variable name.');

        $this->assert->object($node->setVarName('test'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeExternalCounter');
        $this->assert->exception(function () use ($node) {
            $node->setVarName(123);
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');
        
        $this->assert->exception(function () use ($node) {
            $node->verify();
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
        ->hasMessage('Node external counter name has not set.');
        
        $this->assert->object($node->setCounterName('test'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeExternalCounter');
        $this->assert->exception(function () use ($node) {
            $node->setCounterName(123);
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');
        
        $this->assert->variable($node->verify())->isNull();

    }
}