<?php

namespace App\Repository;

use App\Entity\StudentPresence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Tests\Fixtures\ToString;

/**
 * @method StudentPresence|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentPresence|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentPresence[]    findAll()
 * @method StudentPresence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentPresenceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StudentPresence::class);
    }

    // /**
    //  * @return UserAttendance[] Returns an array of UserAttendance objects
    //  */

    public function findByDate(\DateTime $date)
    {
        return $this->createQueryBuilder('u')
            ->join('u.rollCall','r')
            ->andWhere('r.date BETWEEN :min_date AND :max_date')
            ->andWhere('u.present = false')
            ->setParameter('min_date', $date)
            ->setParameter('max_date', (clone $date)->modify('+ 23 hours 59 minutes 59 seconde'))
            ->setMaxResults(30)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?UserAttendance
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
