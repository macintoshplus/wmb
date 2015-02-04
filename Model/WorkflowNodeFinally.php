<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

/**
 * Special type of start node that is activated when a workflow execution is
 * cancelled.
 *
 * Incoming nodes: 0
 * Outgoing nodes: 1
 *
 */
class WorkflowNodeFinally extends WorkflowNodeStart
{
}

