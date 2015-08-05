<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

use Symfony\Bundle\TwigBundle\TwigEngine;
use \Swift_Mailer;
use \Twig_Environment;
use JbNahan\Bundle\WorkflowManagerBundle\Exception\WorkflowExecutionException;

/**
 * WorkflowNodeEmail class
 *
 * @author Jean-Baptiste Nahan <jbnahan at gmail dot com>
 **/
class WorkflowNodeEmail extends WorkflowNode
{

    const AFFECTED_EXECUTION_USER = 'user@execution.tld';

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
        if (is_array($configuration['to'])) {
            $configuration['to'] = implode(',', $configuration['to']);
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
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML(\DOMElement $element)
    {
        $configuration = array('from' => $element->getAttribute('from'));

        $tos = $element->getElementsByTagName('to');
        $toEmails = [];
        foreach ($tos as $to) {
            $toEmails[] = $to->nodeValue;
        }
        $configuration['to']=$toEmails;

        $body = $element->getElementsByTagName('body')->item(0);
        $configuration['body']=base64_decode($body->nodeValue);
        $subject = $element->getElementsByTagName('subject')->item(0);
        $configuration['subject']=($subject->nodeValue);

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML(\DOMElement $element)
    {
        $element->setAttribute('from', $this->configuration['from']);
        foreach ($this->getTo() as $toEmail) {
            $element->appendChild($element->ownerDocument->createElement('to', $toEmail));
        }
        $element->appendChild($element->ownerDocument->createElement('body', base64_encode($this->configuration['body'])));
        $element->appendChild($element->ownerDocument->createElement('subject', ($this->configuration['subject'])));
        
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
        if (is_array($to)) {
            $filter = array_filter($to);
            $unique = array_unique($filter);
            $to = implode(',', $unique);
        }
        $this->configuration['to'] = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return explode(',', $this->configuration['to']);
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

        if (!$execution->hasMailer()) {
            $err = "Unable to use this node if mailer service is not set";
            $execution->critical($err);
            throw new WorkflowExecutionException($err);
        }
        if (!$execution->hasTwig()) {
            $err = "Unable to use this node if twig service is not set";
            $execution->critical($err);
            throw new WorkflowExecutionException($err);
        }

        $subject = $execution->renderTemplate($this->configuration['subject']);
        $body = $execution->renderTemplate($this->configuration['body']);

        $finalTo = $this->configuration['to'];
        //if ('user' === $this->configuration['to']) {
        //Ajoute les emails des utilisateurs de l'execution
        if (false !== strpos($finalTo, WorkflowNodeEmail::AFFECTED_EXECUTION_USER)) {
            $array = $execution->getRoles();
            if (null === $array || 0 === count($array)) {
                $err = "Unable to use 'user' in 'to' email field";
                $execution->critical($err);
                throw new WorkflowExecutionException($err);
            }

            $toDef = '';
            foreach ($array as $user) {
                $toDef .= (('' === $toDef)? '':',').$user->getEmail();
            }
            $finalTo = str_replace(WorkflowNodeEmail::AFFECTED_EXECUTION_USER, $toDef, $finalTo);
        }
        //remet en array et retire les valeurs vides
        $toArray = array_filter(explode(',', $finalTo));

        $message = \Swift_Message::newInstance()
        ->setSubject(html_entity_decode($subject))
        ->setFrom($this->configuration['from'])
        ->setTo($toArray)
        ->setBody($body, 'text/html');

        $recipient = array();
        $sent = $execution->mailerSend($message, $recipient);
        $execution->info(sprintf("Sent email at %d recipient.", $sent));

        if (0 < count($recipient)) {
            $execution->warning("Unable to send at : ".implode(', ', $recipient));
            return false;
        }

        $this->activateNode($execution, $this->outNodes[0]);

        return parent::execute($execution);

    }
}
