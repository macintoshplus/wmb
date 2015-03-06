<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowNodeReviewUniqueForm extends Units\Test
{
    public function test_execute_without_data()
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

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array('internal_name'=>'form_1'));
        
        $continueNode  = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintContinue' );
        $elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintElse' );

        $node->addSelectOutNode($continueNode, $elseNode);

        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
/*
        $mockExecute->setVariable('form_1_review',array(array('data'=>'toto')));

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
*/
        $this->assert->array($mockExecute->getActivatedNodes())->hasSize(1);
        //var_dump($mockExecute->getActivatedNodes());

    }

    public function test_execute_with_data_error()
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

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array('internal_name'=>'form_1'));
        

        $continueNode  = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintContinue' );
        $elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintElse' );

        $node->addSelectOutNode($continueNode, $elseNode);

        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $mockExecute->setVariable('form_1_review',array(array('data'=>'toto')));

        $this->assert->boolean($node->execute($mockExecute))->isTrue();

        $this->assert->array($mockExecute->getActivatedNodes())->hasSize(2);
        $this->assert->array($mockExecute->getActivatedNodes())->contains($elseNode);
        //var_dump($mockExecute->getActivatedNodes());

    }


    public function test_execute_with_data_no_error()
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

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array('internal_name'=>'form_1'));
        

        $continueNode  = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintContinue' );
        $elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction( 'PrintElse' );

        $node->addSelectOutNode($continueNode, $elseNode);

        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $mockExecute->setVariable('form_1_review',array());

        $this->assert->boolean($node->execute($mockExecute))->isTrue();

        $this->assert->array($mockExecute->getActivatedNodes())->hasSize(2);
        $this->assert->array($mockExecute->getActivatedNodes())->contains($continueNode);
        //var_dump($mockExecute->getActivatedNodes());

    }




    public function test_verify()
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

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array());
        $node->addInNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart());

        $continueNode  = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('PrintContinue');
        $elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('PrintElse');

        $node->addSelectOutNode($continueNode, $elseNode);

        $this->assert->exception(function () use ($node) {
            $node->verify();
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
        ->hasMessage('Node Review unique form has no form internal name.');

        $node->setInternalName('test1');
        $this->assert->variable($node->verify())->isNull();
        

    }


    public function test_has_role()
    {
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array('internal_name'=>'form_1', 'roles'=>array('test1','test5')));

        $this->assert->boolean($node->hasRoleUsername('test1'))->isTrue();
        $this->assert->boolean($node->hasRoleUsername('test2'))->isFalse();


        $this->assert->boolean($node->hasRoles(array('test2', 'test1')))->isTrue();
        $this->assert->boolean($node->hasRoles(array('test2', 'test3')))->isFalse();

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array('internal_name'=>'form_1', 'roles'=>array()));

        $this->assert->boolean($node->hasRoleUsername('test2'))->isFalse();

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array('internal_name'=>'form_1', 'roles'=>null));

        $this->assert->boolean($node->hasRoleUsername('test2'))->isFalse();
        
    }


    public function test_get_set()
    {
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array('internal_name'=>'form_1', 'roles'=>array('test1','test5')));

        $this->assert->object($node->setInternalName('t1'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm');
        $this->assert->object($node->setRoles(array('test')))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm');
        
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

        $this->assert->exception(function () use ($node, $mockExecute) {
            $node->getReviewData($mockExecute);
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException')
        ->message->contains('t1_review');

        $this->assert->exception(function () use ($node, $mockExecute) {
            $node->getFormData($mockExecute);
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException')
        ->hasMessage('Variable "t1" does not exist.');


        $mockExecute->getMockController()->getVariable =array('toto');

        $this->assert->array($node->getReviewData($mockExecute))
        ->hasSize(1)->contains('toto');

        $mockExecute->getMockController()->getVariable =array('tata');

        $this->assert->string($node->getFormData($mockExecute))
        ->isEqualTo('tata');
    }
}