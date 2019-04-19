<?php

namespace App\Repository;

use App\Entity\StudentPresence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
