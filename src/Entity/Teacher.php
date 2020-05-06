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
     * @ORM\OneToMany(targetEntity="Lesson", mappedBy="teacher")
     */
    private $lessons;

    public function __construct()
    {
        $this->classGroups = new ArrayCollection();
        $this->lessons = new ArrayCollection();
    }

    public function teachToStudent(Student $student): bool
    {
        /** @var ClassGroup $classGroup */
        foreach ($this->classGroups as $classGroup) {
            foreach ($classGroup->getStudents() as $classStudent) {
                if ($classStudent->getId() === $student->getId()) {
                    return true;
                }
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
     * @return Collection|Lesson[]
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addAttendance(Lesson $attendance): self
    {
        if (!$this->lessons->contains($attendance)) {
            $this->lessons[] = $attendance;
            $attendance->setTeacher($this);
        }

        return $this;
    }

    public function removeAttendance(Lesson $attendance): self
    {
        if ($this->lessons->contains($attendance)) {
            $this->lessons->removeElement($attendance);
            // set the owning side to null (unless already changed)
            if ($attendance->getTeacher() === $this) {
                $attendance->setTeacher(null);
            }
        }

        return $this;
    }
}
