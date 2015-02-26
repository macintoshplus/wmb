<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Conditions;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;

//new WorkflowConditionVariable( 'i', new WorkflowConditionIsEqual( 10 ) );

class WorkflowConditionIsInstanceOf extends Units\Test
{
    public function test_not_set()
    {
        $condition = new \JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface');

        $mock = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface();

        $this->assert->boolean($condition->evaluate(array()))->isFalse();
        $this->assert->boolean($condition->evaluate("vfdf"))->isFalse();


        $this->assert->boolean($condition->evaluate($mock))->isTrue();

        $this->assert->string($condition->getValue())->isEqualTo('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeFormResponseInterface');
    }
}
