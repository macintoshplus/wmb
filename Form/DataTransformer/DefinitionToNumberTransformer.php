<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use JbNahan\Bundle\WorkflowManagerBundle\Manager\DefinitionManager;
use Acme\TaskBundle\Entity\Issue;

class DefinitionToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param ObjectManager $em
     */
    public function __construct(DefinitionManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($issue)
    {
        if (null === $issue) {
            return "";
        }

        return $issue->getId();
    }

    /**
     * Transforms a string (id) to an object (definition).
     *
     * @param  string $id
     * @return Definition|null
     * @throws TransformationFailedException if object (definition) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $definition = $this->em->getById($id);

        if (null === $definition) {
            throw new TransformationFailedException(sprintf(
                'Le paramétrage numéro "%s" ne peut pas être trouvé!',
                $id
            ));
        }

        return $definition;
    }
}