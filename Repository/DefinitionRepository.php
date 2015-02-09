<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use JbNahan\Bundle\WorkflowManagerBundle\Entity\DefinitionSearch;

/**
 * DefinitionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DefinitionRepository extends EntityRepository
{
    /**
     * @param DefinitionSearch $param
     * @return Querybuilder
     */
    public function getQbWithSearch(DefinitionSearch $param)
    {
        $qb = $this->createQueryBuilder('w');
        if (null !== $param->getName()) {
            $qb->andWhere($qb->expr()->like('w.name', $qb->expr()->literal('%'.$param->getName().'%')));
        }
        //Limite aux roles
        if (is_array($param->getRolesForUpdate()) && 0 < count($param->getRolesForUpdate())) { //
            $expr = '';
            foreach ($param->getRolesForUpdate() as $role) {
                $expr .= ($expr===''? '':' OR ') . 'w.rolesForUpdate like \'%'.$role.'%\'';
            }

            $qb->andWhere('(' . $expr . ')');

        }
        
        if (null !== $param->getPublishedAt()) {
            if ($param->getPublishedAt() instanceof \DateTime) {
                $qb->andWhere('w.publishedAt = :datepublish')
                    ->setParameter('datepublish', $param->getPublishedAt());
            }
            
        }

        if (null !== $param->isPublished()) {
            //c'est publié
            if (is_bool($param->isPublished()) && true === $param->isPublished()) {
                $qb->andWhere($qb->expr()->isNotNull('w.publishedAt'));
            }
            //c'est pas publié
            if (is_bool($param->isPublished()) && false === $param->isPublished()) {
                $qb->andWhere($qb->expr()->isNull('w.publishedAt'));
            }
            
        }
        
        if (null !== $param->getArchivedAt()) {
            if ($param->getArchivedAt() instanceof \DateTime) {
                $qb->andWhere('w.archivedAt = :datearchive')
                    ->setParameter('datearchive', $param->getArchivedAt());
            }
            
        }
        
        if (null !== $param->isArchived()) {
            //c'est archivé
            if (is_bool($param->isArchived()) && true === $param->isArchived()) {
                $qb->andWhere($qb->expr()->isNotNull('w.archivedAt'));
            }
            //c'est pas archivé
            if (is_bool($param->isArchived()) && false === $param->isArchived()) {
                $qb->andWhere($qb->expr()->isNull('w.archivedAt'));
            }
            
        }
        /*if (null !== $param->get()) {
            $qb->andWhere('w. =')
            ->setParameter('', $param->get());
        }*/

        return $qb;
    }
}
