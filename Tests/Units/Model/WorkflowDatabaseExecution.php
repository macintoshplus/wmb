<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowDatabaseExecution extends Units\Test
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
        $mockSwift->getMockController()->send = 1;
        $mockTwig = new Mock\Twig_Environment();
        $mockTwig->getMockController()->render = '';
        //, $mockLogger, $mockSwift, $mockTwig

        $execution = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig);
        
        $mockDefinition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $execution->workflow = $mockDefinition;

        $this->assert->boolean($execution->hasLogger())->isTrue();
        $this->assert->boolean($execution->hasMailer())->isTrue();
        $this->assert->boolean($execution->hasTwig())->isTrue();

    }
}
