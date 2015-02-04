<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Factory;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;

class WorkflowNodeFactory extends Units\Test
{
	public function test_makeNode()
	{

		//$mock = new Mock\Symfony\Component\DependencyInjection\Container();

		$controller = new \atoum\mock\controller();
		$controller->__construct = function() {};
		$mockSwift = new Mock\Swift_Mailer(new Mock\Swift_Transport(), $controller);
		//$obj = new \stdClass();
		//$mock->getMockController()->get = $obj;

		$factory = new \JbNahan\Bundle\WorkflowManagerBundle\Factory\WorkflowNodeFactory($mockSwift, new Mock\Twig_Environment());

		$node = $factory->createNode('Email');

		$this->assert->object($node)->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEmail');

		$this->assert->exception(function() use ($factory){
			$factory->createNode('none');
		})->hasMessage('Class JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodenone does not exists !');

		$node = $factory->createNode('ComputeExecutionName');

		$this->assert->object($node)->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeComputeExecutionName');
		
		$node = $factory->createNode('EnableUserCancellable');

		$this->assert->object($node)->isInstanceOf('JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnableUserCancellable');
	}
}

