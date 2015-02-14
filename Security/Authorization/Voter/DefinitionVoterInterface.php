<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter;

interface DefinitionVoterInterface
{
    public function getRolesForUse();

    public function getRolesForUpdate();
}
