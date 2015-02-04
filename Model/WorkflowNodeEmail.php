<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Swift_Mailer;
use \Twig_Environment;

/**
 * WorkflowNodeEmail class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeEmail extends WorkflowNode
{
	private $mailer;

	private $twig;

    /**
     * @param array $configuration
     */
	public function __construct( array $configuration = null )
    {

        if ( !isset( $configuration['from'] ) )
        {
            $configuration['from'] = 'exemple@toto.fr';
        }
        if ( !isset( $configuration['to'] ) )
        {
            $configuration['to'] = 'exemple@toto.fr';
        }
        if ( !isset( $configuration['subject'] ) )
        {
            $configuration['subject'] = 'Email from Workflow';
        }

        if ( !isset( $configuration['body'] ) )
        {
            $configuration['body'] = "Hello,\nThis is a email send by Workflow.\n\nBye";
        }

        parent::__construct( $configuration );
    }

    public function execute( WorkflowExecution $execution )
    {

        if ( !isset( $this->mailer ) ){
        	throw new \Exception("Enable to use this node if mailer service is not set");
        }
        if ( !isset( $this->twig ) ){
        	throw new \Exception("Enable to use this node if twig service is not set");
        }

        $variables = $execution->getVariables();
        $variables['execution_id'] = $execution->getId();
        $variables['execution_name'] = $execution->getName();
        $variables['execution_ended'] = $execution->hasEnded();
        $variables['execution_cancelled'] = $execution->isCancelled();
        $variables['workflow_name'] = $execution->workflow->name;
        $variables['workflow_id'] = $execution->workflow->id;
        $variables['now'] = new \DateTime();

        $subject = $this->twig->render($this->configuration['subject'], $variables);
        $body = $this->twig->render($this->configuration['body'], $variables);

        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($this->configuration['from'])
        ->setTo($this->configuration['to'])
        ->setBody($body) ;
        
		$recipient = array();
		$sent = $this->mailer->send($message, $recipient);

		if (!empty($recipient)) {
			return false;
		}

		$this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );

    }
    /**
     * defini le service d'envoie des emails
     * @param Swit_Mailer $mailer
     */
    public function setMailer(Swift_Mailer $mailer)
    {
    	$this->mailer = $mailer;
    }

    /**
     * defini le service de rendu des templates
     * @param Twig $twig;
     */
    public function setTwig(Twig_Environment $twig)
    {
    	$this->twig = $twig;
    }

} // END class WorkflowNodeEmail 