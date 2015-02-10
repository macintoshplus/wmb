<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ExecutionVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CANCEL = 'cancel';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = array(
            'JbNahan\Bundle\WorkflowManagerBundle\Entity\Execution',
            'JbNahan\Bundle\WorkflowManagerBundle\Model\WorkflowExecution'
        );

        return in_array($class, $supportedClass, true);
    }

    /**
     * @var \JbNahan\Bundle\WorkflowManagerBundle\Entity\Definition $entity
     */
    public function vote(TokenInterface $token, $entity, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($entity))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
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
            case self::VIEW:
                // the data object could have for example a method isPrivate()
                // which checks the Boolean attribute $private
                if (null === $entity->getRoles() || 0 === count($entity->getRoles()) || in_array($user->getUsername(), $user->getRoles(), $entity->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::EDIT:
                // we assume that our data object has a method getOwner() to
                // get the current owner user entity for this data object
                // if (null !== $entity->getRolesForUpdate() && 0 < count(array_intersect($user->getRoles(), $entity->getRolesForUpdate()))) {
                //     return VoterInterface::ACCESS_GRANTED;
                // }
                // break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
