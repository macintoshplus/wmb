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

		$this->assert->exception(function() use ($workflow) {
			$workflow->setEmailParameters('22', array());
		})->hasMessage('Invalid param values !');

		$this->assert->exception(function() use ($workflow) {
			$workflow->setEmailParameters('22', array('to'=>null, 'from'=>null, 'body'=>null, 'subject'=>null));
		})->hasMessage('Unable to set email parameters for node id 22 (node not found or not type Email)');
		
		$config = array('to'=>'me@me2.fr', 'from'=>'you@you2.fr', 'body'=>'Write here all you want', 'subject'=>'My Subject');
		
		$workflow->setEmailParameters('4', $config);

		$this->assert->string($nodeEmail->getFrom())->isEqualTo($config['from']);
		$this->assert->string($nodeEmail->getTo())->isEqualTo($config['to']);
		$this->assert->string($nodeEmail->getBody())->isEqualTo($config['body']);
		$this->assert->string($nodeEmail->getSubject())->isEqualTo($config['subject']);
	}
}