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

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function(){};
        

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

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function(){};
        

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
        
        //Vérifie que la réponse a bien été traitée
        $this->assert->boolean($mockExecute->hasVariable('form_test1'))->isTrue();
        //var_dump($mockExecute->getVariable('form_test1'));
        $this->assert->array($mockExecute->getVariable('form_test1'))->hasSize(2);
        //Vérifie qu'il attent toujours les réponses
        $this->assert->array($mockExecute->getWaitingFor())
            ->hasSize(2)
            ->hasKeys(array('form_test1_response', 'form_test1_continue'));

        //remplacement des données

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 0;
        $mock->getMockController()->getName = 'replace';

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
        //remplacement des données avec un ID faux

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId =50;
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

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function(){};
        

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

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();
        $mock->getMockController()->getId = 0;
        $mock->getMockController()->getName = 'replace';
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

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function(){};
        

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

        $mockExecute = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowDatabaseExecution($entityManager, $definitionService, $security, null, $controller);
        $mockExecute->getMockController()->getId = 1;
        //$mockExecute->getMockController()->loadFromVariableHandlers = function(){};
        

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
}