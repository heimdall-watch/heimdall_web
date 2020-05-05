<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 */
class Admin extends User
{
    protected $roles = ['ROLE_ADMIN'];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ClassGroup", mappedBy="admins")
     */
    private $classGroups;

    public function __construct()
    {
        $this->classGroups = new ArrayCollection();
    }

    public function getClassGroups()
    {
        return $this->classGroups;
    }

    public function setClassGroups($classGroups): Admin
    {
        $this->classGroups = $classGroups;

        return $this;
    }

    public function addClassGroup(ClassGroup $classGroup): self
    {
        if (!$this->classGroups->contains($classGroup)) {
            $this->classGroups[] = $classGroup;
        }

        return $this;
    }
}
