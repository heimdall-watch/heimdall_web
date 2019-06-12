<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeacherRepository")
 */
class Teacher extends User
{
    protected $roles = ['ROLE_TEACHER'];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ClassGroup", inversedBy="teachers")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $classGroups;

    /**
     * @ORM\OneToMany(targetEntity="RollCall", mappedBy="teacher")
     */
    private $rollCalls;

    public function __construct()
    {
        $this->classGroups = new ArrayCollection();
        $this->rollCalls = new ArrayCollection();
    }

    public function teachToStudent(Student $student): bool
    {
        /** @var ClassGroup $classGroup */
        foreach ($this->classGroups as $classGroup) {
            if ($classGroup->getStudents()->contains($student)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection|ClassGroup[]
     */
    public function getClassGroups(): Collection
    {
        return $this->classGroups;
    }

    public function addClassGroup(ClassGroup $classGroup): self
    {
        if (!$this->classGroups->contains($classGroup)) {
            $this->classGroups[] = $classGroup;
        }

        return $this;
    }

    public function removeClassGroup(ClassGroup $classGroup): self
    {
        if ($this->classGroups->contains($classGroup)) {
            $this->classGroups->removeElement($classGroup);
        }

        return $this;
    }

    /**
     * @return Collection|RollCall[]
     */
    public function getRollCalls(): Collection
    {
        return $this->rollCalls;
    }

    public function addAttendance(RollCall $attendance): self
    {
        if (!$this->rollCalls->contains($attendance)) {
            $this->rollCalls[] = $attendance;
            $attendance->setTeacher($this);
        }

        return $this;
    }

    public function removeAttendance(RollCall $attendance): self
    {
        if ($this->rollCalls->contains($attendance)) {
            $this->rollCalls->removeElement($attendance);
            // set the owning side to null (unless already changed)
            if ($attendance->getTeacher() === $this) {
                $attendance->setTeacher(null);
            }
        }

        return $this;
    }
}
