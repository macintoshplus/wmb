<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Manager;

use Doctrine\ORM\EntityManager;

class ExecutionManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    private function getRepository()
    {
        return $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Execution');
    }
}
