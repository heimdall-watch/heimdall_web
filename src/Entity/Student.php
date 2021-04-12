<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Serializer\Groups({"Default"})
     * @Assert\NotBlank()
     */
    private $classGroup;

    /**
     * @ORM\OneToMany(targetEntity="StudentPresence", mappedBy="student")
     * @Serializer\Groups({"Default"})
     */
    private $presences;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClassStudents"})
     */
    private $photoDescription;

    /**
     * @Vich\UploadableField(mapping="students_photos", fileNameProperty="photo")
     * @var File
     * @Assert\File(
     *      maxSize="5242880",
     *      mimeTypes = {
     *          "image/png",
     *          "image/jpeg",
     *          "image/jpg",
     *      }
     * )
     * @Serializer\Exclude()
     */
    private $photoFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     * @Serializer\Exclude()
     */
    private $updatedAt;


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
     *
     * Carefull here : a presence can be an absence (????)
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

        if (null !== $photo) {
            $this->updatedAt = new \DateTime();
        }



        return $this;
    }

    //TODO: MAKE THIS WORK
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * @return mixed
     */
    public function getPhotoDescription()
    {
        return $this->photoDescription;
    }

    /**
     * @param mixed $photoDescription
     */
    public function setPhotoDescription($photoDescription): void
    {
        $this->photoDescription = $photoDescription;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Student
     */
    public function setUpdatedAt(\DateTime $updatedAt): Student
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}
