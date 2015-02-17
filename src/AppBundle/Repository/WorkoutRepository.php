<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 15.02.15
 * Time: 14:44
 */

namespace AppBundle\Repository;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Paginator;

class WorkoutRepository extends EntityRepository {

    public function getPaginatedList(User $user, Paginator $paginator, $pageNumber)
    {
        $query = $this->createQueryBuilder('w')
            ->select(array('partial w.{id, startDatetime, distanceMeters, totalTimeSeconds}', 'partial s.{id, displayName}'))
            ->where('w.user = :user')
            ->setParameter('user', $user)
            ->join('w.sport', 's')
            ->orderBy('w.startDatetime', 'DESC')
            ->getQuery();

        return $paginator->paginate($query, $pageNumber, 50);
    }

}