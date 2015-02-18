<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter;

interface NodeVoterInterface
{
    /**
     * @return boolean
     */
    public function getRoles();

    /**
     * @param string $username
     * @return booblean
     */
    public function hasRoleUsername($username);

    public function hasRoles(array $roles);
}
