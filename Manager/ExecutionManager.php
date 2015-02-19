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
    public function __construct(EntityManager $entityManager, SecurityContextInterface $security)
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

    public function getByDefinitionId($id)
    {
        $param = new Entity\ExecutionSearch();
        $param->setDefinition($id);
        $qb = $this->getQbFromSearch($param);

        return $qb->getQuery()->getResult();
    }

    private function getRepository()
    {
        return $this->entityManager->getRepository('JbNahanWorkflowManagerBundle:Execution');
    }
}
