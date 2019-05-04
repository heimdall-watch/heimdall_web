<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentPresenceRepository")
 */
class StudentPresence
{
    const EXCUSE_SICK = 'sick';
    const EXCUSE_FAMILY = 'family';
    const EXCUSE_TRANSPORT = 'transport';
    const EXCUSE_WORK = 'work';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="RollCall", inversedBy="studentPresences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rollCall;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="presences")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Type("EntityId<App\Entity\Student>")
     * @Assert\NotBlank()
     */
    private $student;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     */
    private $present;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("integer")
     * @Assert\GreaterThan(0)
     * @Assert\LessThan(propertyPath="rollCall.getDuration()")
     */
    private $late;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $excuse; // TODO : Constantes + Possibilité d'entrer un intitulé manuellement ?

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $excuseProof; // TODO Lien vers le justificatif uploadé ?

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type("boolean")
     */
    private $excuseValidated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRollcall(): ?RollCall
    {
        return $this->rollCall;
    }

    public function setRollcall(?RollCall $rollCall): self
    {
        $this->rollCall = $rollCall;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getPresent(): ?bool
    {
        return $this->present;
    }

    public function setPresent(bool $present): self
    {
        $this->present = $present;

        return $this;
    }

    public function getLate(): ?int
    {
        return $this->late;
    }

    public function setLate(?int $late): self
    {
        $this->late = $late;

        return $this;
    }

    public function getExcuse(): ?string
    {
        return $this->excuse;
    }

    public function setExcuse(?string $excuse): self
    {
        $this->excuse = $excuse;

        return $this;
    }

    public function getExcuseProof(): ?string
    {
        return $this->excuseProof;
    }

    public function setExcuseProof(?string $excuseProof): self
    {
        $this->excuseProof = $excuseProof;

        return $this;
    }

    public function getExcuseValidated(): ?bool
    {
        return $this->excuseValidated;
    }

    public function setExcuseValidated(?bool $excuseValidated): self
    {
        $this->excuseValidated = $excuseValidated;

        return $this;
    }
}
