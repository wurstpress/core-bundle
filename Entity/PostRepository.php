<?php

namespace Wurstpress\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    public function getPaginator($request, $entityManager)
    {
        $dql = "
        SELECT p,c,d
        FROM WurstpressCoreBundle:Post p
        LEFT JOIN p.collection c
        LEFT JOIN c.documents d
        ORDER BY p.created DESC
        ";
        $query = $entityManager->createQuery($dql)
            ->setFirstResult($request->get('offset') ?: 0)
            ->setMaxResults($request->get('limit') ?: 10);

        return new Paginator($query, $fetchJoinCollection = true);
    }
}
