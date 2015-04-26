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

    public function search(User $user, \DateTime $startDt = null, \DateTime $endDt = null, array $sportIds = null){
        $builder = $this->createQueryBuilder('w')
            ->select(array('partial w.{id, startDatetime, distanceMeters, totalTimeSeconds}'))
            ->where('w.user = :user')
            ->setParameter('user', $user)
            ->orderBy('w.startDatetime', 'DESC');
        if($startDt){
            $builder->andWhere('w.startDatetime >= :startDt')->setParameter('startDt', $startDt);
        }
        if($endDt){
            $builder->andWhere('w.startDatetime <= :endDt')->setParameter('endDt', $endDt);
        }
        if($sportIds){
            $builder->andWhere('w.sport IN (:sportIds)')->setParameter('sportIds', $sportIds);
        }

        return $builder->getQuery()->getResult();
    }

}