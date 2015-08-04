<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowNodeForm extends Units\Test
{
    public function test_instanciate()
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
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function () {};


        //Tests de la configuration
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array());

        $this->assert->boolean($node->doConfirmContinue())->isTrue();
        $this->assert->string($node->getInternalName())->match('#^form_[0-9]{10}$#');

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->boolean($node->getMaxResponse())->isFalse();

        unset($node);

        /**
         * Max 1 réponse et pas d'auto continue
         */
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false);

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);

        $this->assert->boolean($node->doConfirmContinue())->isTrue();
        $this->assert->string($node->getInternalName())->isEqualTo('form_test1');

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->integer($node->getMaxResponse())->isEqualTo(1);

        unset($node);

        /**
         * Max 1 réponse et auto continue
         */
        $config['auto_continue']= true;

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);

        $this->assert->boolean($node->doConfirmContinue())->isFalse();
        $this->assert->string($node->getInternalName())->isEqualTo('form_test1');

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->integer($node->getMaxResponse())->isEqualTo(1);

        unset($node);

        /**
         * Max < Min
         */
        $config['min_response']= 2;

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->integer($node->getMaxResponse())->isEqualTo(1);


        unset($node);


        /**
         * Min is string
         */
        $config['min_response']= "a";

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->integer($node->getMaxResponse())->isEqualTo(1);

        unset($node);

        /**
         * Max is string
         */
        $config['max_response']= "a";

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->boolean($node->getMaxResponse())->isFalse();

        unset($node, $config);

    }

    public function test_execute_many_manual()
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
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function () {};


        /**
         * test execution multiple
         * réassignement pas l'ajout de l'ID dans les données
         */
        $config=array('min_response'=>1,
            'max_response'=>false,
            'internal_name'=>'form_test1',
            'auto_continue'=>false);

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';

        $mockExecute->setVariable('form_test1_response', $mock);
        //Execute le node
        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());
        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'tata';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->mock($mock)->call('setAnsweredAt')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(2);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //remplacement des données
        $keys = array_keys($mockExecute->getVariable('form_test1'));

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = $keys[0];
        $mock->getMockController()->getName = 'replace';
        $mock->getMockController()->getAnsweredAt = new \DateTime();

        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->mock($mock)->call('setUpdatedAt')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(2);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));
        //remplacement des données avec un ID faux

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId =50;
        $mock->getMockController()->getName = 'replace invalid key';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->mock($mock)->call('setAnsweredAt')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(3);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));
    }


    public function test_execute_many_withDelete()
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
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function () {};


        /**
         * test execution multiple
         * réassignement pas l'ajout de l'ID dans les données
         */
        $config=array('min_response'=>1,
            'max_response'=>3,
            'internal_name'=>'form_test1',
            'auto_continue'=>false);

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';
        $mockExecute->setVariable('form_test1_response', $mock);
        //Execute le node
        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'tata';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(2);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //remplacement des données
        $keys = array_keys($mockExecute->getVariable('form_test1'));

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = $keys[0];
        $mock->getMockController()->getName = 'replace';
        $mock->getMockController()->getAnsweredAt = new \DateTime();
        $mock->getMockController()->getDeletedAt = new \DateTime();

        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->mock($mock)->call('getId')->twice();
        $this->mock($mock)->call('setId')->never();
        $this->mock($mock)->call('getAnsweredAt')->never();
        $this->mock($mock)->call('getDeletedAt')->once();
        $this->mock($mock)->call('setUpdatedAt')->never();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);

        $varDeleted = 'form_test1'.\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm::PREFIX_DELETED;
        $this->assert->boolean($mockExecute->hasVariable($varDeleted))->isTrue();
        $this->assert->array($mockExecute->getVariable($varDeleted))->hasSize(1);

        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));


            //suppression d'un object inconnu
        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 'jkhgfd';
        $mock->getMockController()->getName = 'replace';
        $mock->getMockController()->getAnsweredAt = new \DateTime();
        $mock->getMockController()->getDeletedAt = new \DateTime();

        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->mock($mock)->call('getId')->twice();
        $this->mock($mock)->call('setId')->never();
        $this->mock($mock)->call('getAnsweredAt')->never();
        $this->mock($mock)->call('getDeletedAt')->once();
        $this->mock($mock)->call('setUpdatedAt')->never();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);

        $varDeleted = 'form_test1'.\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm::PREFIX_DELETED;
        $this->assert->boolean($mockExecute->hasVariable($varDeleted))->isTrue();
        $this->assert->array($mockExecute->getVariable($varDeleted))->hasSize(1);

        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

    }

    public function test_execute_many_max_3_manual()
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
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function () {};


        /**
         * test execution multiple
         * réassignement pas l'ajout de l'ID dans les données
         */
        $config=array('min_response'=>1,
            'max_response'=>3,
            'internal_name'=>'form_test1',
            'auto_continue'=>false);

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';
        $mockExecute->setVariable('form_test1_response', $mock);
        //Execute le node
        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'tata';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(2);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //remplacement des données
        $keys = array_keys($mockExecute->getVariable('form_test1'));

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = $keys[0];
        $mock->getMockController()->getName = 'replace';
        $mock->getMockController()->getAnsweredAt = new \DateTime();

        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->mock($mock)->call('getId')->twice();
        $this->mock($mock)->call('setId')->once();
        $this->mock($mock)->call('getAnsweredAt')->twice();
        $this->mock($mock)->call('setUpdatedAt')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(2);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));


        //remplacement des données avec un ID faux

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 50;
        $mock->getMockController()->getName = 'replace invalid key';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(3);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        $mockExecute->setVariable('form_test1_continue', true);
        //var_dump($mockExecute->getVariables());
        //var_dump($mockExecute->getWaitingFor());
        $this->assert->boolean($node->execute($mockExecute))->isTrue();
        //var_dump($mockExecute->getVariables());

        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(3);
        //Vérifie qu'il attent toujours les réponses
        //var_dump($mockExecute->getWaitingFor());
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //ajout de donnée > max + confirmation

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 50;
        $mock->getMockController()->getName = 'overload data';
        $mockExecute->setVariable('form_test1_response', $mock);
        $mockExecute->setVariable('form_test1_continue', true);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isTrue();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(3);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));
    }

    public function test_unit_auto()
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
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function () {};


        /**
         * test execution simple + auto_continue
         * en cas de réassignement, il faut remplacer les valeurs
         */
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>true);

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->activate($mockExecute);

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->integer($node->getMaxResponse())->isEqualTo(1);
        $this->assert->boolean($node->doConfirmContinue())->isFalse();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(1)
            ->hasKeys(array('form_test1_response'));

        //Enregistre une réponse

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';
        $mockExecute->setVariable('form_test1_response', $mock);
        //Execute le node
        $this->assert->boolean($node->execute($mockExecute))->isTrue();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(1)
            ->hasKeys(array('form_test1_response'));

        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'tata';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isTrue();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(1)
            ->hasKeys(array('form_test1_response'));

        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 0;
        $mock->getMockController()->getName = 'tata';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isTrue();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->twice();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(1)
            ->hasKeys(array('form_test1_response'));


        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 5;
        $mock->getMockController()->getName = 'new value';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isTrue();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->twice();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(1)
            ->hasKeys(array('form_test1_response'));


        //Rééxecution du node sans saisie de nouvelles données
        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());
        //$mockExecute->setVariable('form_test1_response', array('id'=>5, 'data1'=>'new value'));
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(1)
            ->hasKeys(array('form_test1_response'));

    }

    public function test_unit_manual()
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
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function () {};


        /**
         * test execution simple + auto_continue
         * en cas de réassignement, il faut remplacer les valeurs
         */
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false);

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->activate($mockExecute);

        $this->assert->integer($node->getMinResponse())->isEqualTo(1);
        $this->assert->integer($node->getMaxResponse())->isEqualTo(1);
        $this->assert->boolean($node->doConfirmContinue())->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'toto';
        $mockExecute->setVariable('form_test1_response', $mock);
        //Execute le node
        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //$this->mock($mock)->call('setId')->once();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);

        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = null;
        $mock->getMockController()->getName = 'tata';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->once();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 0;
        $mock->getMockController()->getName = 'tata';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->twice();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));


        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 5;
        $mock->getMockController()->getName = 'new value';
        $mockExecute->setVariable('form_test1_response', $mock);
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();
        $this->mock($mock)->call('setId')->withArguments(0)->once();
        $this->mock($mock)->call('getId')->twice();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));


        //Rééxecution du node sans saisie de nouvelles données
        //Enregistre une réponse
        //var_dump($mockExecute->getVariables());
        //$mockExecute->setVariable('form_test1_response', array('id'=>5, 'data1'=>'new value'));
        //var_dump($mockExecute->getVariables());

        //Execute le node
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //Confirmation de la saisie
        $mockExecute->setVariable('form_test1_continue', true);
        //var_dump($mockExecute->getVariables());
        //var_dump($mockExecute->getWaitingFor());
        $this->assert->boolean($node->execute($mockExecute))->isTrue();
        //var_dump($mockExecute->getVariables());

        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(1);
        //Vérifie qu'il attent toujours les réponses
        //var_dump($mockExecute->getWaitingFor());
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

    }


    public function test_has_role()
    {
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array('test1','test5'));

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        
        $this->assert->boolean($node->hasRoleUsername('test1'))->isTrue();
        $this->assert->boolean($node->hasRoleUsername('test2'))->isFalse();

        $this->assert->boolean($node->hasRoles(array('test2', 'test1')))->isTrue();
        $this->assert->boolean($node->hasRoles(array('test2', 'test3')))->isFalse();

        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>null);

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        
        $this->assert->boolean($node->hasRoleUsername('test2'))->isFalse();

        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array());

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        
        $this->assert->boolean($node->hasRoleUsername('test2'))->isFalse();
    }

    public function test_set_get()
    {
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array('test1','test5'));

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        
        $this->assert->object($node->setMaxResponse(2))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm');

        $this->assert->object($node->setMinResponse(1))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm');

        $this->assert->exception(function () use ($node){
            $node->setMaxResponse('465fgsd');
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');

        $this->assert->exception(function () use ($node){
            $node->setMinResponse('465fgsd');
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');

        $this->assert->object($node->setInternalName('1'))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm');

        $this->assert->object($node->setAutoContinue(false))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm');

        $this->assert->boolean($node->getAutoContinue())->isFalse();

        $this->assert->exception(function () use ($node){
            $node->setAutoContinue('465fgsd');
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');

        $this->assert->object($node->setRoles(array('test')))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm');

    }

    public function test_single_response()
    {
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array('test1','test5'));

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        
        $this->assert->boolean($node->dosingleResponse())->isTrue();

        $config=array('min_response'=>1,
            'max_response'=>2,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array('test1','test5'));

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        
        $this->assert->boolean($node->dosingleResponse())->isFalse();

    }

    public function test_responseIsEnough()
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
        $mockTwig = new Mock\Twig_Environment();
        //, $mockLogger, $mockSwift, $mockTwig

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, $mockLogger, $mockSwift, $mockTwig, null, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function () {};

        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array('test1','test5'));

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->activate($mockExecute);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->assert->boolean($node->responseIsEnough($mockExecute))->isFalse();

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 5;
        $mock->getMockController()->getName = 'new value';
        $mockExecute->setVariable('form_test1_response', $mock);

        $this->assert->boolean($node->execute($mockExecute))->isFalse();

        $this->assert->boolean($node->responseIsEnough($mockExecute))->isTrue();

        $mockExecute->setVariable('form_test1', array($mock, $mock));
        
        $this->assert->boolean($node->responseIsEnough($mockExecute))->isFalse();

    }

    public function test_verifyAutoContinue()
    {

        $config=array('min_response'=>2,
            'max_response'=>2,
            'internal_name'=>'form_test1',
            'auto_continue'=>true,
            'roles'=>array('test1','test5'));
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->addInNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart());
        
        $this->assert->integer($node->getMinResponse())->isEqualTo(2);
        $this->assert->integer($node->getMaxResponse())->isEqualTo(2);
        
        $this->assert->exception(function() use ($node) {
            $node->verify();
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
        ->message->contains('many response required and auto_continue enable');


        $node->setMinResponse(1);
        $node->setMaxResponse(5);
        $node->setAutoContinue(true);
        $this->assert->exception(function() use ($node) {
            $node->verify();
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
        ->message->contains('many response required and auto_continue enable');
        //JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException
        

        $node->setMinResponse(5);
        $node->setMaxResponse(1);
        $this->assert->exception(function() use ($node) {
            $node->verify();
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
        ->message->contains('min response greater than max response');
    }

    public function testVerifyMinEqualMaxAuto()
    {
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>true,
            'roles'=>array('test1','test5'));
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->addInNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart());
        
        $this->variable($node->verify())->isNull();
    }


    public function testVerifyMinEqualMaxNoAuto()
    {
        $config=array('min_response'=>1,
            'max_response'=>1,
            'internal_name'=>'form_test1',
            'auto_continue'=>true,
            'roles'=>array('test1','test5'));
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->addInNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart());
        
        $this->variable($node->verify())->isNull();
    }
    public function testVerifyNoMax()
    {
        $config=array('min_response'=>1,
            'max_response'=>false,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array('test1','test5'));
        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $node->addOutNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd());
        $node->addInNode(new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart());
        
        $this->variable($node->verify())->isNull();
    }



    public function testExportXml()
    {
        $storage = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDefinitionStorageXml();
        $def = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $def->definitionStorage = $storage;
        $config=array('min_response'=>1,
            'max_response'=>false,
            'internal_name'=>'form_test1',
            'auto_continue'=>false,
            'roles'=>array('role_1','role_2'));

        $node = new \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm($config);
        $def->startNode->addOutNode($node);
        $node->addOutNode($def->endNode);

        $element = $storage->saveToDocument($def, 1);

        $this->assert->string($element->saveXML())
            ->contains('form_test1')
            ->contains('auto_continue="false"')
            ->contains('max_response="0"')
            ->contains('roles')
            ->contains('role_1')
            ->contains('role_2');
        
        $document = new \DOMDocument('1.0', 'UTF-8');
        $nodeXml = $document->createElement('node');
        $node->configurationToXML($nodeXml);

        $config = \JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm::configurationFromXML($nodeXml);

        $this->assert->array($config)->hasSize(5)->contains('form1');
    }
}
