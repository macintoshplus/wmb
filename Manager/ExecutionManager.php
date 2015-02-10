<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Manager;

use JbNahan\Bundle\WorkflowManagerBundle\Entity as Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ExecutionManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    private $security;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, SecurityContextInterace $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @param ExecutionSearch $param
     * @return QueryBuilder
     */
    public function getQbFromSearch(Entity\ExecutionSearch $param)
    {
        return $this->getRepository()->getQbSearch($param);
    }

    private function getRepository()
    {
        return $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Execution');
    }
}
