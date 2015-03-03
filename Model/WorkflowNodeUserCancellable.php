<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * WorkflowNodeUserCancellable class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
abstract class WorkflowNodeUserCancellable extends WorkflowNode
{
    protected $newStatusForCancellable = null;

    public function execute(WorkflowExecution $execution)
    {
        $execution->setCancellable($this->newStatusForCancellable);
        
        $execution->info(sprintf('Set worflow cancellable : %s', (($this->newStatusForCancellable)? 'True':'False')));

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);

    }
}
