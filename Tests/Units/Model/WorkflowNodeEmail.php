<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowNodeEmail extends Units\Test
{
    public function test_send_ok()
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
        $mockSwift->getMockController()->send = 1;
        $mockTwig = new Mock\Twig_Environment();
        $mockTwig->getMockController()->render = '';
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, $controller);
        $mockExecute->getMockController()->getId = 1;

        $mockDefinition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $mockExecute->workflow = $mockDefinition;

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail();
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());


        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isTrue();

        $this->mock($mockSwift)->call('send')->once();
        $this->mock($mockTwig)->call('render')->twice();
        $this->mock($mockLogger)->call('info')->once();
        $this->mock($mockLogger)->call('warning')->never();
        $this->mock($mockLogger)->call('error')->never();


    }
    public function test_send_ko()
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
        // $mockSwift->getMockController()->send = function($arg1, &$arg2){
        //     $arg2[]='toto@toto.fr';
        //     return 0;
        // };
        $mockTwig = new Mock\Twig_Environment();
        $mockTwig->getMockController()->render = '';
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        $mockExecute->getMockController()->mailerSend = function ($arg1, &$arg2){
            $arg2[]='toto@toto.fr';
            return 0;
        };
        
        $mockDefinition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $mockExecute->workflow = $mockDefinition;

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail();
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());


        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->mock($mockExecute)->call('mailerSend')->once();
        $this->mock($mockExecute)->call('renderTemplate')->twice();
        $this->mock($mockLogger)->call('info')->once();
        $this->mock($mockLogger)->call('warning')->once();
        $this->mock($mockLogger)->call('error')->never();

    }
}