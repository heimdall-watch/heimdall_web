<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function findLastWeek($user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.dateStart BETWEEN :min_date AND :max_date')
            ->andWhere('u.teacher = :teacher')
            ->setParameter('min_date', (new \DateTime('1 week ago')))
            ->setParameter('max_date', (new \DateTime()))
            ->setParameter('teacher',$user)
            ->setMaxResults(30)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findNextLesson($user){
        return $this->createQueryBuilder('u')
            ->andWhere('u.dateStart > :date')
            ->andWhere('u.teacher = :teacher')
            ->setParameter('date', (new \DateTime()))
            ->setParameter('teacher',$user)
            ->setMaxResults(30)
            ->getQuery()
            ->getResult()
            ;
    }
    
    public function getFindAllQuery()
    {
        $qb = $this->createQueryBuilder('lesson');

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Attendance[] Returns an array of Attendance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Attendance
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
