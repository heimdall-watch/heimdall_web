<?php

namespace App\Repository;

use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TeacherRepository extends UserRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Teacher::class);
    }
}
