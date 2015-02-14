<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter;

interface ExecutionVoterInterface
{

    public function getRoles();

    public function hasRoleUsername($username);
}
