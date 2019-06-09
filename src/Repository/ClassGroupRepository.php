<?php

namespace App\Repository;

use App\Entity\ClassGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ClassGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassGroup[]    findAll()
 * @method ClassGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClassGroup::class);
    }

    public function getFindAllQuery()
    {
        $qb = $this->createQueryBuilder('class');

        return $qb->getQuery();
    }

    /*
    public function findOneBySomeField($value): ?Group
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
