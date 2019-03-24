<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RollCallRepository")
 */
class RollCall
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClassGroup", inversedBy="rollCalls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="rollCalls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $teacher;

    /**
     * @ORM\OneToMany(targetEntity="StudentPresence", mappedBy="rollCall", orphanRemoval=true)
     */
    private $studentPresences;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    public function __construct()
    {
        $this->studentPresences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection|StudentPresence[]
     */
    public function getStudentPresences(): Collection
    {
        return $this->studentPresences;
    }

    public function addUserAttendance(StudentPresence $userAttendance): self
    {
        if (!$this->studentPresences->contains($userAttendance)) {
            $this->studentPresences[] = $userAttendance;
            $userAttendance->setRollcall($this);
        }

        return $this;
    }

    public function removeUserAttendance(StudentPresence $userAttendance): self
    {
        if ($this->studentPresences->contains($userAttendance)) {
            $this->studentPresences->removeElement($userAttendance);
            // set the owning side to null (unless already changed)
            if ($userAttendance->getRollcall() === $this) {
                $userAttendance->setRollcall(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
