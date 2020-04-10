<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClassGroupRepository")
 */
class ClassGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClass"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClass"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClass"})
     */
    private $university;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClass"})
     */
    private $UFR;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetClass"})
     */
    private $formation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="classGroup")
     * @ORM\OrderBy({"lastname" = "ASC", "firstname" = "ASC"})
     * @Serializer\Groups({"Default", "Deserialization"})
     */
    private $students;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Teacher", mappedBy="classGroups")
     * @Serializer\Groups({"Default", "Deserialization"})
     */
    private $teachers;

    /**
     * @ORM\OneToMany(targetEntity="Lesson", mappedBy="classGroup")
     * @Serializer\Groups({"Default", "Deserialization"})
     */
    private $lessons;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Admin", inversedBy="classGroups", cascade={"persist"})
     * @Serializer\Groups({"Default", "Deserialization"})
     */
    private $admins;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->teachers = new ArrayCollection();
        $this->lessons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(User $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setClassGroup($this);
        }

        return $this;
    }

    public function removeStudent(User $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getClassGroup() === $this) {
                $student->setClassGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Lesson[]
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function getAdmins()
    {
        return $this->admins;
    }

    public function setAdmins($admins): ClassGroup
    {
        $this->admins = $admins;

        return $this;
    }

    public function getUniversity()
    {
        return $this->university;
    }

    public function setUniversity($university): ClassGroup
    {
        $this->university = $university;

        return $this;
    }

    public function getUFR()
    {
        return $this->UFR;
    }

    public function setUFR($UFR): ClassGroup
    {
        $this->UFR = $UFR;

        return $this;
    }

    public function getFormation()
    {
        return $this->formation;
    }

    public function setFormation($formation): ClassGroup
    {
        $this->formation = $formation;

        return $this;
    }

    public function addAttendance(Lesson $attendance): self
    {
        if (!$this->lessons->contains($attendance)) {
            $this->lessons[] = $attendance;
            $attendance->setClassGroup($this);
        }

        return $this;
    }

    public function removeAttendance(Lesson $attendance): self
    {
        if ($this->lessons->contains($attendance)) {
            $this->lessons->removeElement($attendance);
            // set the owning side to null (unless already changed)
            if ($attendance->getClassGroup() === $this) {
                $attendance->setClassGroup(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|User[]
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(User $teacher): self
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers[] = $teacher;
            $teacher->setClassGroup($this);
        }

        return $this;
    }

    public function removeTeacher(User $teacher): self
    {
        if ($this->teachers->contains($teacher)) {
            $this->teachers->removeElement($teacher);
            // set the owning side to null (unless already changed)
            if ($teacher->getClassGroup() === $this) {
                $teacher->setClassGroup(null);
            }
        }

        return $this;
    }

}
