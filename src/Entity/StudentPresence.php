<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentPresenceRepository")
 * @Vich\Uploadable
 */
class StudentPresence
{
    const EXCUSES = ['sick', 'family', 'transport', 'work', 'other'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="RollCall", inversedBy="studentPresences")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"Default", "Deserialization", "GetStudentPresences"})
     */
    private $rollCall;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="presences")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Serializer\Type("EntityId<App\Entity\Student>")
     * @Serializer\Groups({"Default", "Deserialization"})
     * @Assert\NotBlank()
     */
    private $student;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     * @Assert\Type("boolean")
     */
    private $present;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     * @Assert\Type("integer")
     * @Assert\GreaterThan(0)
     */
    private $late;

    /**
     * @Vich\UploadableField(mapping="excuses_photos", fileNameProperty="excuseProof")
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     */
    private $excuse; // TODO : Constantes + Possibilité d'entrer un intitulé manuellement ?

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
     */
    private $excuseProof; // TODO Lien vers le justificatif uploadé ?

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Groups({"Default", "GetRollcall", "Deserialization", "GetStudentPresences"})
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

    /**
     * @Serializer\VirtualProperty("getStudent")
     * @Serializer\Groups({"GetRollcall"})
     * @Serializer\Type("App\Entity\Student")
     * @Serializer\MaxDepth(1)
     * @return Student|null
     */
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

    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    public function setPhotoFile(File $excuseProof = null)
    {
        $this->photoFile = $excuseProof;
        return $this;
    }
}
