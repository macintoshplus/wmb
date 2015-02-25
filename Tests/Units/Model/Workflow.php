<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Model;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class Workflow extends Units\Test
{
    public function test_get_email()
    {
        $workflow = new \JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $nodeAction = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('Action1');

        $config = array('to'=>'me@me.fr', 'from'=>'you@you.fr', 'body'=>'Write here all you want !', 'subject'=>'Subject');
        $nodeEmail = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail($config);

        $workflow->startNode->addOutNode($nodeAction);
        $nodeAction->addOutNode($nodeEmail);
        $nodeEmail->addOutNode($workflow->endNode);

        //var_dump();
        $arr = $workflow->getEmailParameters();
        $this->assert->array($arr)->hasSize(1)->hasKey(4);
    }

    public function test_update_email()
    {
        $workflow = new \JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $nodeAction = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('Action1');

        $config = array('to'=>'me@me.fr', 'from'=>'you@you.fr', 'body'=>'Write here all you want !', 'subject'=>'Subject');
        $nodeEmail = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail($config);

        $workflow->startNode->addOutNode($nodeAction);
        $nodeAction->addOutNode($nodeEmail);
        $nodeEmail->addOutNode($workflow->endNode);

        //var_dump($workflow->getEmailParameters());

        $this->assert->exception(function () use ($workflow) {
            $workflow->setEmailParameters('22', array());
        })->hasMessage('Invalid param values !');

        $this->assert->exception(function () use ($workflow) {
            $workflow->setEmailParameters('22', array('to'=>null, 'from'=>null, 'body'=>null, 'subject'=>null));
        })->hasMessage('Unable to set email parameters for node id 22 (node not found or not type Email)');

        $config = array('to'=>'me@me2.fr', 'from'=>'you@you2.fr', 'body'=>'Write here all you want', 'subject'=>'My Subject');

        $workflow->setEmailParameters('4', $config);

        $this->assert->string($nodeEmail->getFrom())->isEqualTo($config['from']);
        $this->assert->array($nodeEmail->getTo())->hasSize(1)->contains($config['to']);
        $this->assert->string($nodeEmail->getBody())->isEqualTo($config['body']);
        $this->assert->string($nodeEmail->getSubject())->isEqualTo($config['subject']);
    }

    public function test_get_date()
    {
        $workflow = new \JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $nodeAction = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('Action1');

        $config = array('internal_name'=>'me@me.fr', 'out_date'=>new \DateTime('2015-02-01'));
        $nodeControl = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeControlForm($config);
        $nodeControl->setName('test date');

        $workflow->startNode->addOutNode($nodeAction);
        $nodeAction->addOutNode($nodeControl);
        $nodeControl->addSelectOutNode($workflow->endNode, $workflow->endNode);

        $arr = $workflow->getDateParameters();
        //var_dump($arr);
        $this->assert->array($arr)->hasSize(1)->hasKey(4);
    }


    public function test_set_date()
    {
        $workflow = new \JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        $nodeAction = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('Action1');

        $config = array('internal_name'=>'me@me.fr', 'out_date'=>new \DateTime('2015-02-01'));
        $nodeControl = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeControlForm($config);
        $nodeControl->setName('test date');

        $workflow->startNode->addOutNode($nodeAction);
        $nodeAction->addOutNode($nodeControl);
        $nodeControl->addSelectOutNode($workflow->endNode, $workflow->endNode);

        $arr = $workflow->getDateParameters();
        //var_dump($arr);
        $this->assert->array($arr)->hasSize(1)->hasKey(4);

        $date = new \DateTime('2012-03-15');
        $workflow->setDateParameters(4, $date);
        $this->assert->datetime($nodeControl->getOutDate())->isEqualTo($date);

        $this->assert->exception(function () use ($workflow) {
            $workflow->setDateParameters('22', new \DateTime('2012-03-15'));
        })->hasMessage('Unable to set date parameters for node id 22 (node not found or not type ControlForm)');

    }

    public function test_set_get()
    {
        $workflow = new \JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');
        
        $this->assert->variable($workflow->definitionStorage)->isNull();
        $this->assert->boolean($workflow->id)->isFalse();
        $this->assert->object($workflow->startNode)->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeStart');
        $this->assert->object($workflow->endNode)->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd');
        $this->assert->object($workflow->finallyNode)->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFinally');
        $this->assert->string($workflow->name)->isEqualTo('test');
        $this->assert->integer($workflow->version)->isEqualTo(1);
        $this->assert->array($workflow->nodes)->hasSize(1);

        $this->assert->exception(function () use ($workflow) {
            $workflow->toto;
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyNotFoundException');

        $this->assert->exception(function () use ($workflow) {
            $workflow->toto = 123;
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyNotFoundException');

        $this->assert->exception(function () use ($workflow) {
            $workflow->name = 123;
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');
        
        $this->assert->exception(function () use ($workflow) {
            $workflow->id = 'fsg';
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');

        $this->assert->exception(function () use ($workflow) {
            $workflow->definitionStorage = 'dfqdfq';
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');

        $this->assert->exception(function () use ($workflow) {
            $workflow->version = 'dfqdfq';
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BaseValueException');

        
        $this->assert->exception(function () use ($workflow) {
            $workflow->startNode = 'dfqdfq';
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyPermissionException');

        
        $this->assert->exception(function () use ($workflow) {
            $workflow->endNode = 'dfqdfq';
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyPermissionException');

        
        $this->assert->exception(function () use ($workflow) {
            $workflow->finallyNode = 'dfqdfq';
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyPermissionException');

        
        $this->assert->exception(function () use ($workflow) {
            $workflow->nodes = 'dfqdfq';
        })->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Exception\BasePropertyPermissionException');

        /*$this->assert->variable($workflow->id = 1)->isNull();
        $this->assert->variable($workflow->name = 'dfe')->isNull();
        $this->assert->variable($workflow->version = 100)->isNull();
*/

        $this->assert->boolean(isset($workflow->definitionStorage))->isTrue();
        $this->assert->boolean(isset($workflow->id))->isTrue();
        $this->assert->boolean(isset($workflow->name))->isTrue();
        $this->assert->boolean(isset($workflow->startNode))->isTrue();
        $this->assert->boolean(isset($workflow->endNode))->isTrue();
        $this->assert->boolean(isset($workflow->finallyNode))->isTrue();
        $this->assert->boolean(isset($workflow->nodes))->isTrue();
        $this->assert->boolean(isset($workflow->version))->isTrue();
        $this->assert->boolean(isset($workflow->toto))->isFalse();

        $this->assert->object($workflow->setRolesForUpdate(array('123')))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow');
        $this->assert->array($workflow->getRolesForUpdate())->hasSize(1)->contains('123');

        $this->assert->object($workflow->setRolesForUse(array('123')))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow');
        $this->assert->array($workflow->getRolesForUse())->hasSize(1)->contains('123');


        $this->assert->boolean($workflow->isPublished())->isFalse();
        $this->assert->object($workflow->setPublishedAt(new \DateTime('2014-02-25')))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow');
        $this->assert->boolean($workflow->isPublished())->isTrue();


        $this->assert->boolean($workflow->isArchived())->isFalse();
        $this->assert->object($workflow->setArchivedAt(new \DateTime('2014-02-25')))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow');
        $this->assert->boolean($workflow->isArchived())->isTrue();

        $this->assert->variable($workflow->getParent())->isNull();
        $this->assert->object($workflow->setParent(100))->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow');
        $this->assert->integer($workflow->getParent())->isEqualTo(100);

        $this->assert->integer($workflow->count())->isEqualTo(1);


        $this->assert->boolean($workflow->isInteractive())->isFalse();
        $input = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeInput(array('test'));
        $input->addInNode($workflow->startNode);

        $this->assert->boolean($workflow->isInteractive())->isTrue();
    }
}
