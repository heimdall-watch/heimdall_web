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

    public function findAbsencesRetards($user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.student = :student')
            ->andWhere('u.present = false OR u.late is not NULL')
            ->setParameter('student',$user)
            ->leftJoin('u.lesson', 'r')
            ->orderBy('r.dateStart', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPending()
    {
        return $this->createQueryBuilder('u')
            ->where('(u.present = false OR u.late IS NOT NULL) AND (u.excuseValidated = false OR u.excuseValidated IS NULL)')
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
