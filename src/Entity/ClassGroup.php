<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 */
class ClassGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="classGroup")
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity="RollCall", mappedBy="classGroup")
     */
    private $rollCalls;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->rollCalls = new ArrayCollection();
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
            $attendance->setClassGroup($this);
        }

        return $this;
    }

    public function removeAttendance(RollCall $attendance): self
    {
        if ($this->rollCalls->contains($attendance)) {
            $this->rollCalls->removeElement($attendance);
            // set the owning side to null (unless already changed)
            if ($attendance->getClassGroup() === $this) {
                $attendance->setClassGroup(null);
            }
        }

        return $this;
    }
}
