<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentPresenceRepository")
 * @Vich\Uploadable
 */
class StudentPresence
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetStudentPresences"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Lesson", inversedBy="studentPresences")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"Default", "Deserialization", "GetStudentPresences"})
     */
    private $lesson;

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
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetStudentPresences"})
     * @Assert\Type("boolean")
     */
    private $present;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetStudentPresences"})
     * @Assert\DateTime()
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
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetStudentPresences"})
     */
    private $excuseDescription; // TODO : Constantes + Possibilité d'entrer un intitulé manuellement ?

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetStudentPresences"})
     */
    private $excuseProof; // TODO Lien vers le justificatif uploadé ?

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Groups({"Default", "Getlesson", "Deserialization", "GetStudentPresences"})
     * @Assert\Type("boolean")
     */
    private $excuseValidated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getlesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setlesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty("getStudent")
     * @Serializer\Groups({"Getlesson"})
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

    public function getLate(): ?DateTime
    {
        return $this->late;
    }

    public function setLate(?DateTime $late): self
    {
        $this->late = $late;

        return $this;
    }

    public function getExcuseDescription(): ?string
    {
        return $this->excuseDescription;
    }

    public function setExcuseDescription(?string $excuseDescription): self
    {
        $this->excuseDescription = $excuseDescription;

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
