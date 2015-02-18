<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowVisitableInterface;

class NodeVoter implements VoterInterface
{
    const EDIT = 'edit';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::EDIT
        ));
    }

    public function supportsClass($class)
    {
        return true;
    }

    /**
     * @var \JbNahan\Bundle\WorkflowManagerBundle\Entity\Definition $entity
     */
    public function vote(TokenInterface $token, $entity, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($entity)) ||
            !($entity instanceof \JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter\NodeVoterInterface)
            ) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for EDIT'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        //If Admin, all access
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch($attribute) {
            case self::EDIT:
                // the data object could have for example a method isPrivate()
                // which checks the Boolean attribute $private
                if (null === $entity->getRoles() || 0 === count($entity->getRoles()) || $entity->hasRoleUsername($user->getUsername()) || $entity->hasRoles($user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
