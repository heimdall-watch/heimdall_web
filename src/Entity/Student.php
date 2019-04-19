<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 * @Vich\Uploadable
 */
class Student extends User
{
    protected $roles = ['ROLE_STUDENT'];

    /**
     * @ORM\ManyToOne(targetEntity="ClassGroup", inversedBy="students")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classGroup;

    /**
     * @ORM\OneToMany(targetEntity="StudentPresence", mappedBy="student")
     */
    private $presences;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @Vich\UploadableField(mapping="students_photos", fileNameProperty="photo")
     * @var File
     */
    private $photoFile;




    public function __construct()
    {
        $this->presences = new ArrayCollection();
    }

    public function getClassGroup(): ?ClassGroup
    {
        return $this->classGroup;
    }

    public function setClassGroup(?ClassGroup $classGroup): self
    {
        $this->classGroup = $classGroup;

        return $this;
    }

    /**
     * @return Collection|StudentPresence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addAttendance(StudentPresence $attendance): self
    {
        if (!$this->presences->contains($attendance)) {
            $this->presences[] = $attendance;
            $attendance->setStudent($this);
        }

        return $this;
    }

    public function removeAttendance(StudentPresence $attendance): self
    {
        if ($this->presences->contains($attendance)) {
            $this->presences->removeElement($attendance);
            // set the owning side to null (unless already changed)
            if ($attendance->getStudent() === $this) {
                $attendance->setStudent(null);
            }
        }

        return $this;
    }

    public function setPhotoFile(File $photo = null)
    {
        $this->photoFile = $photo;
        return $this;
    }
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

}
