<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

class WorkflowRole implements WorkflowRoleInterface
{
    protected $username;

    protected $email;

    /**
     * @param string $username
     * @return WorkflowRole
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $email
     * @return WorkflowRole
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function __toString()
    {
        return $this->getUsername();
    }
}
