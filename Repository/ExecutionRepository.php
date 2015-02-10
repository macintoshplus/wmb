<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use JbNahan\Bundle\WorkflowManagerBundle\Entity\ExecutionSearch;

/**
 * ExecutionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExecutionRepository extends EntityRepository
{
    /**
     * @param int $workflowId
     * @return ArrayCollection
     */
    public function getExecutionById($executionId)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e, s')
        ->leftJoin('e.states', 's')
        ->where('e.id = :id')
        ->setParameter('id', $executionId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param ExecutionSearch $param
     * @return QueryBuilder
     */
    public function getQbSearch(ExecutionSearch $param)
    {
        $qb = $this->createQueryBuilder('e');

        //Partie obligatoire

        //Limite aux roles
        if (is_array($param->getDefinitionList()) &&
            0 < count($param->getDefinitionList()) &&
            is_array($param->getRoles()) &&
            0 < count($param->getRoles())) { //
            
            $expr = '';
            foreach ($param->getRoles() as $role) {
                $expr .= ($expr===''? '':' OR ') . 'e.roles like \'%'.$role.'%\'';
            }

            $qb->andWhere($qb->expr()->or('(' . $expr . ')', $qb->expr()->in('e.definition', $param->getDefinitionList())));

        }

        //Partie recherche
        return $qb;
    }
}
