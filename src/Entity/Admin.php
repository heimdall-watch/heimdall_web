<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


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

    public function getClassGroups()
    {
        return $this->classGroups;
    }

    public function setClassGroups($classGroups): Admin
    {
        $this->classGroups = $classGroups;

        return $this;
    }
}
