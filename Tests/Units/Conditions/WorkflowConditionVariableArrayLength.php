<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Conditions;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;

//new WorkflowConditionVariable( 'i', new WorkflowConditionIsEqual( 10 ) );

class WorkflowConditionVariableArrayLength extends Units\Test
{
	public function test_not_set()
	{
		$equal = new \JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual(1);
		$condition = new \JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionVariableArrayLength('test', $equal);

		$this->assert->boolean($condition->evaluate(array()))->isFalse();
		$this->assert->boolean($condition->evaluate("vfdf"))->isFalse();


		$this->assert->boolean($condition->evaluate(array('test'=>"csdgf")))->isFalse();
		$this->assert->boolean($condition->evaluate(array('test'=>array())))->isFalse();
		$this->assert->boolean($condition->evaluate(array('test'=>array('data1'=>false))))->isTrue();
	}
}