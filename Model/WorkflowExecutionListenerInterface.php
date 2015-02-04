<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;
/**
 * Interface for workflow execution listeners.
 *
 */
interface WorkflowExecutionListenerInterface
{
    /**
     * Debug severity constant.
     */
     const DEBUG          = 1;

    /**
     * Success audit severity constant.
     */
     const SUCCESS_AUDIT  = 2;

    /**
     * Failed audit severity constant.
     */
     const FAILED_AUDIT   = 4;

     /**
      * Info severity constant.
      */
     const INFO           = 8;

     /**
      * Notice severity constant.
      */
     const NOTICE         = 16;

     /**
      * Warning severity constant.
      */
     const WARNING        = 32;

     /**
      * Error severity constant.
      */
     const ERROR          = 64;

     /**
      * Fatal severity constant.
      */
     const FATAL          = 128;

    /**
     * Called to inform about events.
     *
     * @param string  $message
     * @param int $type
     */
    public function notify( $message, $type = WorkflowExecutionListenerInterface::INFO );
}
?>
