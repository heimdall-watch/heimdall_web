<?php

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Admin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admin[]    findAll()
 * @method Admin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * TODO : Query filter ("like" not working with postgres)
     *
     * Returns only admins (not the superadmin)
     *
     * @return array
     */
    public function findAllAdmins(): array
    {
        return \array_filter($this->findAll(), function (Admin $admin) {
            return !$admin->hasRole('ROLE_SUPER_ADMIN');
        });
    }

    // TODO : Query filter ("like" not working with postgres)
    public function hasSuperAdmin(): bool
    {
        $admins = $this->findAll();
        foreach ($admins as $admin) {
            if ($admin->hasRole('ROLE_SUPER_ADMIN')) {
                return true;
            }
        }
        return false;
    }
}
