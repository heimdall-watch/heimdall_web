<?php

namespace App\Repository;

use App\Entity\EmailAlert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EmailAlert|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailAlert|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailAlert[]    findAll()
 * @method EmailAlert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailAlertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EmailAlert::class);
    }

    public function getFindAllQuery()
    {
        $qb = $this->createQueryBuilder('alert');

        return $qb->getQuery();
    }

    public function getShouldSend()
    {
        return $this->createQueryBuilder('a')
            ->where('a.lastSent IS NULL')
            ->orWhere("CURRENT_DATE() >= DATE_ADD(a.lastSent, a.periodicity, 'day')")
            ->getQuery()
            ->getResult();
    }
}
