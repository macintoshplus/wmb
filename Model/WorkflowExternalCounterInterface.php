<?php
namespace JbNahan\Bundle\WorkflowManagerBundle\Model;




/**
 * WorkflowExternalCounterInterface
 */
interface WorkflowExternalCounterInterface
{
	/**
	 * Return the next value for the counter name.
	 * @param string $name
	 * @return int
	 */
	public function getNext($name);
}
