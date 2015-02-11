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
    public function __construct(array $configuration = null)
    {

        if (!isset( $configuration['from'])) {
            $configuration['from'] = 'exemple@toto.fr';
        }
        if (!isset( $configuration['to'])) {
            $configuration['to'] = 'exemple@toto.fr';
        }
        if (!isset( $configuration['subject'])) {
            $configuration['subject'] = 'Email from Workflow';
        }

        if (!isset( $configuration['body'])) {
            $configuration['body'] = "Hello,\nThis is a email send by Workflow.\n\nBye";
        }

        parent::__construct($configuration);
    }

    /**
     * @param string
     * @return WorkflowNodeEmail
     */
    public function setFrom($from)
    {
        $this->configuration['from'] = $from;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->configuration['from'];
    }

    /**
     * @param string
     * @return WorkflowNodeEmail
     */
    public function setTo($to)
    {
        $this->configuration['to'] = $to;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->configuration['to'];
    }

    /**
     * @param string
     * @return WorkflowNodeEmail
     */
    public function setSubject($subject)
    {
        $this->configuration['subject'] = $subject;
        
        return $this;
    }


    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->configuration['subject'];
    }

    public function setBody($body)
    {
        $this->configuration['body'] = $body;
        
        return $this;
    }


    /**
     * @return string
     */
    public function getBody()
    {
        return $this->configuration['body'];
    }

    public function execute(WorkflowExecution $execution)
    {

        if (!isset( $this->mailer)) {
            throw new \Exception("Enable to use this node if mailer service is not set");
        }
        if (!isset( $this->twig)) {
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

        if ('user' === $this->configuration['to']) {
            $array = $execution->getRoles();
            if (null === $array || 0 === count($array)) {
                throw new \Exception("Unable to use 'user' in 'to' email field");
            }

            $toDef = '';
            foreach ($array as $user) {
                $toDef .= (('' === $toDef)? '':', ').$user->getEmail();
            }
        } else {
            $toDef = $this->configuration['to'];
        }
        

        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($this->configuration['from'])
        ->setTo($toDef)
        ->setBody($body) ;
        
        $recipient = array();
        $sent = $this->mailer->send($message, $recipient);

        if (!empty($recipient)) {
            return false;
        }

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);

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
}
