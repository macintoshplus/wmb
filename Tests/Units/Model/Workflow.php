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
}
