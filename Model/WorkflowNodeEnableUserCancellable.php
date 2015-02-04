<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * WorkflowNodeEnableUserCancellable class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeEnableUserCancellable extends WorkflowNodeUserCancellable
{
	protected $newStatusForCancellable = true;
}