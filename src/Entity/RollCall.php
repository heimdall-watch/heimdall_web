<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RollCallRepository")
 */
class RollCall
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClassGroup", inversedBy="rollCalls")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Serializer\Type("EntityId<App\Entity\ClassGroup>")
     * @Serializer\Groups({"Default", "Deserialization"})
     * @Assert\NotBlank()
     */
    private $classGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="rollCalls")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Serializer\Type("EntityId<App\Entity\Teacher>")
     * @Serializer\Groups({"Default", "Deserialization"})
     * @Assert\NotBlank()
     */
    private $teacher;

    /**
     * @ORM\OneToMany(targetEntity="StudentPresence", mappedBy="rollCall", orphanRemoval=true, cascade={"persist"})
     * @Serializer\Groups({"Default", "Deserialization"})
     * @Assert\Valid()
     */
    private $studentPresences;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     * @Assert\DateTime()
     * @Assert\LessThan(propertyPath="dateEnd")
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     * @Assert\DateTime()
     * @Assert\GreaterThan(propertyPath="dateStart")
     */
    private $dateEnd;

    public function __construct()
    {
        $this->studentPresences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Serializer\VirtualProperty("getClassGroup")
     * @Serializer\Groups({"GetRollcall"})
     * @Serializer\Type("App\Entity\ClassGroup")
     * @Serializer\MaxDepth(1)
     * @return ClassGroup|null
     */
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
     * @Serializer\VirtualProperty("getTeacher")
     * @Serializer\Groups({"GetRollcall"})
     * @Serializer\Type("App\Entity\Teacher")
     * @Serializer\MaxDepth(1)
     * @return Teacher|null
     */
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
     * @Serializer\VirtualProperty("getStudentPresences")
     * @Serializer\Groups({"GetRollcall"})
     * @Serializer\MaxDepth(2)
     * @return Collection|StudentPresence[]
     */
    public function getStudentPresences(): Collection
    {
        return $this->studentPresences;
    }

    public function addStudentPresence(StudentPresence $studentPresence): self
    {
        if (!$this->studentPresences->contains($studentPresence)) {
            $this->studentPresences[] = $studentPresence;
            $studentPresence->setRollcall($this);
        }

        return $this;
    }

    public function removeStudentPresence(StudentPresence $studentPresence): self
    {
        if ($this->studentPresences->contains($studentPresence)) {
            $this->studentPresences->removeElement($studentPresence);
            // set the owning side to null (unless already changed)
            if ($studentPresence->getRollcall() === $this) {
                $studentPresence->setRollcall(null);
            }
        }

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->dateStart->diff($this->dateEnd)->h;
    }
}
