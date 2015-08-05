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
        $controller->__construct = function () {};

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

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
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
        $controller->__construct = function () {};

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
        // $mockSwift->getMockController()->send = function ($arg1, &$arg2) {
        //     $arg2[]='toto@toto.fr';
        //     return 0;
        // };
        $mockTwig = new Mock\Twig_Environment();
        $mockTwig->getMockController()->render = '';
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        $mockExecute->getMockController()->mailerSend = function ($arg1, &$arg2) {
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

    public function test_getter_setter()
    {
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail();
        $this->assert->string($node->getFrom())->isEqualTo('exemple@toto.fr');
        $this->assert->array($node->getTo())->hasSize(1)->contains('exemple@toto.fr');
        $this->assert->string($node->getSubject())->isEqualTo('Email from Workflow');
        $this->assert->string($node->getBody())->contains('This is a email send by Workflow');

        $this->assert->object($node->setFrom('toto@toto.fr'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail');
        $this->assert->object($node->setSubject('Subject'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail');
        $this->assert->object($node->setTo('toto@toto.fr'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail');
        $this->assert->object($node->setTo(array('toto@toto.fr', 'dede@toto.fr')))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail');
        $this->assert->object($node->setBody('Body contents'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail');

    }


    public function testExportXml()
    {
        $storage = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageXml();
        $def = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $def->definitionStorage = $storage;
        $config=array('from'=>'my_superbe_website@me.fr',
            'to'=>['me@toto.fr', 'arthur@me.fr'],
            'subject'=>'Sujet de mon mail avec du code> &gt;toto',
            'body'=>'Contenu HTML');

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail($config);
        $def->startNode->addOutNode($node);
        $node->addOutNode($def->endNode);

        $element = $storage->saveToDocument($def, 1);

        $this->assert->string($element->saveXML())
            ->contains('arthur@me.fr')
            ->contains('from="my_superbe_website@me.fr"')
            ->contains('me@toto.fr');
        
        $document = new \DOMDocument('1.0', 'UTF-8');
        $nodeXml = $document->createElement('node');
        $node->configurationToXML($nodeXml);

        $config = \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail::configurationFromXML($nodeXml);

        $this->assert->array($config)->hasSize(4)->contains('my_superbe_website@me.fr')->contains('Contenu HTML');
    }
}
