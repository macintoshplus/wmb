<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * WorkflowNodeDisableUserCancellable class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeDisableUserCancellable extends WorkflowNodeUserCancellable
{
	protected $newStatusForCancellable = false;
}