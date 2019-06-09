<?php

namespace App\Repository;

use App\Entity\Student;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StudentRepository extends UserRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function getFindAllQuery()
    {
        $qb = $this->createQueryBuilder('user')
            ->leftJoin('user.classGroup', 'class')
            ->addSelect('class');

        return $qb->getQuery();
    }
}

