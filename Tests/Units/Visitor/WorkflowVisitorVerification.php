<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Tests\Units\Visitor;

use atoum\AtoumBundle\Test\Units;
use Buzz\Browser;
use Mock;


class WorkflowVisitorVerification extends Units\Test
{
	public function test_same_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
	        'min_response'=>1,
	        'max_response'=>1,
	        'internal_name'=>'form1',
	        'auto_continue'=>true,
	        'roles'=>null
	       ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
	        'min_response'=>1,
	        'max_response'=>1,
	        'internal_name'=>'form1',
	        'auto_continue'=>true,
	        'roles'=>null
	       ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->exception(function () use ($verif, $wf) {
			$wf->accept($verif);
		})->isInstanceOf('\JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
		->message->match('#WorkflowNodeForm \(id [0-9]*\) use a existant internal name \'form1\'#');

	}

	public function test_same_form_and_conditional()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array(
			  	'internal_name'=>'form1',
			  	'form_internal_name'=>'form1',
		        'field_internal_name'=>'test'
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->exception(function () use ($verif, $wf) {
			$wf->accept($verif);
		})->isInstanceOf('\JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
		->message->match('#WorkflowNodeConditionalFormValue \(id [0-9]*\) use a existant internal name \'form1\'#');

	}


	public function test_conditional_form_inexistant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array(
			  	'internal_name'=>'form134',
			  	'form_internal_name'=>'form12',
		        'field_internal_name'=>'test'
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->exception(function () use ($verif, $wf) {
			$wf->accept($verif);
		})->isInstanceOf('\JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
		->message->match('#WorkflowNodeConditionalFormValue \(id [0-9]*\) use a inexistant internal name \'form12\'#');

	}



	public function test_add_user_inexistant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAddExecutionUser(array(
			  	'form_internal_name'=>'form12',
		        'field_internal_name'=>'test'
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->exception(function () use ($verif, $wf) {
			$wf->accept($verif);
		})->isInstanceOf('\JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
		->message->match('#WorkflowNodeAddExecutionUser \(id [0-9]*\) use a inexistant internal name \'form12\'#');

	}


	public function test_set_user_inexistant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSetExecutionUser(array(
			  	'form_internal_name'=>'form12',
		        'field_internal_name'=>'test'
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->exception(function () use ($verif, $wf) {
			$wf->accept($verif);
		})->isInstanceOf('\JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
		->message->match('#WorkflowNodeSetExecutionUser \(id [0-9]*\) use a inexistant internal name \'form12\'#');

	}


	public function test_review_unique_form_inexistant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array(
			  	'internal_name'=>'form12'
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->exception(function () use ($verif, $wf) {
			$wf->accept($verif);
		})->isInstanceOf('\JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
		->message->match('#WorkflowNodeReviewUniqueForm \(id [0-9]*\) use a inexistant internal name \'form12\'#');

	}


	public function test_control_form_inexistant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeControlForm(array(
			  	'internal_name'=>'form12',
			  	'out_date'=>new \DateTime()
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->exception(function () use ($verif, $wf) {
			$wf->accept($verif);
		})->isInstanceOf('\JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowInvalidWorkflowException')
		->message->match('#WorkflowNodeControlForm \(id [0-9]*\) use a inexistant internal name \'form12\'#');

	}



	public function test_different_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
	        'min_response'=>1,
	        'max_response'=>1,
	        'internal_name'=>'form1',
	        'auto_continue'=>true,
	        'roles'=>null
	       ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
	        'min_response'=>1,
	        'max_response'=>1,
	        'internal_name'=>'form2',
	        'auto_continue'=>true,
	        'roles'=>null
	       ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->variable($wf->accept($verif))->isNull();

		$this->mock($form1)->call('getInternalName')->atLeastOnce();
		$this->mock($form1)->call('verify')->once();

		$this->mock($form2)->call('getInternalName')->atLeastOnce();
		$this->mock($form2)->call('verify')->once();
	}

	public function test_different_form_and_conditional()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array(
			  	'internal_name'=>'conditional',
			  	'form_internal_name'=>'form1',
		        'field_internal_name'=>'test'
		    ));
		$condition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test1');
		$end = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
		$elseEnd = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeEnd();
        $form2->addSelectOutNode($end, $elseEnd, $condition);

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		
		$this->assert->variable($wf->accept($verif))->isNull();

		$this->mock($form1)->call('getInternalName')->atLeastOnce();
		$this->mock($form1)->call('verify')->once();

		$this->mock($form2)->call('getInternalName')->atLeastOnce();
		$this->mock($form2)->call('verify')->once();

	}


	public function test_conditional_form_existant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeConditionalFormValue(array(
			  	'internal_name'=>'form134',
			  	'form_internal_name'=>'form1',
		        'field_internal_name'=>'test'
		    ));

		$condition = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Conditions\WorkflowConditionIsEqual('test1');
		$elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('PrintElse');
		$merge = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSimpleMerge();
		$merge->addOutNode($wf->endNode);
		$elseNode->addOutNode($merge);

        $form2->addSelectOutNode($merge, $elseNode, $condition);

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		
		$this->assert->variable($wf->accept($verif))->isNull();

		$this->mock($form1)->call('getInternalName')->atLeastOnce();
		$this->mock($form1)->call('verify')->once();

		$this->mock($form2)->call('getInternalName')->atLeastOnce();
		$this->mock($form2)->call('verify')->once();

	}



	public function test_add_user_existant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAddExecutionUser(array(
			  	'form_internal_name'=>'form1',
		        'field_internal_name'=>'test'
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->variable($wf->accept($verif))->isNull();

		$this->mock($form1)->call('getInternalName')->atLeastOnce();
		$this->mock($form1)->call('verify')->once();

		$this->mock($form2)->call('getFormInternalName')->atLeastOnce();
		$this->mock($form2)->call('verify')->once();
		
	}


	public function test_set_user_existant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSetExecutionUser(array(
			  	'form_internal_name'=>'form1',
		        'field_internal_name'=>'test'
		    ));

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$form2->addOutNode($wf->endNode);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->variable($wf->accept($verif))->isNull();

		$this->mock($form1)->call('getInternalName')->atLeastOnce();
		$this->mock($form1)->call('verify')->once();

		$this->mock($form2)->call('getFormInternalName')->atLeastOnce();
		$this->mock($form2)->call('verify')->once();
		
	}


	public function test_review_unique_form_existant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeReviewUniqueForm(array(
			  	'internal_name'=>'form1'
		    ));

		$elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('PrintElse');
		$merge = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSimpleMerge();
		$merge->addOutNode($wf->endNode);
		$elseNode->addOutNode($merge);

		$form2->addSelectOutNode($merge, $elseNode);

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->variable($wf->accept($verif))->isNull();

		$this->mock($form1)->call('getInternalName')->atLeastOnce();
		$this->mock($form1)->call('verify')->once();

		$this->mock($form2)->call('getInternalName')->atLeastOnce();
		$this->mock($form2)->call('verify')->once();
		
	}


	public function test_control_form_existant_form()
	{
		$wf = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\Workflow('test');

		$form1 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeForm(array(
		        'min_response'=>1,
		        'max_response'=>1,
		        'internal_name'=>'form1',
		        'auto_continue'=>true,
		        'roles'=>null
	        ));
		$form2 = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeControlForm(array(
			  	'internal_name'=>'form1',
			  	'out_date'=>new \DateTime()
		    ));

		$elseNode = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeAction('PrintElse');
		$merge = new Mock\JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowNodeSimpleMerge();
		$merge->addOutNode($wf->endNode);
		$elseNode->addOutNode($merge);
		$form2->addSelectOutNode($merge, $elseNode);

		$wf->startNode->addOutNode($form1);
		$form1->addOutNode($form2);

		$verif = new \JbNahan\Bundle\WorkflowManagerBundle\Visitor\WorkflowVisitorVerification();

		$this->assert->variable($wf->accept($verif))->isNull();

		$this->mock($form1)->call('getInternalName')->atLeastOnce();
		$this->mock($form1)->call('verify')->once();

		$this->mock($form2)->call('getInternalName')->atLeastOnce();
		$this->mock($form2)->call('verify')->once();
		
	}
}