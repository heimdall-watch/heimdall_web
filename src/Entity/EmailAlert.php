<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmailAlertRepository")
 */
class EmailAlert
{
    const PERIO_DAILY = 1;
    const PERIO_WEEKLY = 7;
    const PERIO_MONTHLY = 30;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     * In days
     */
    private $periodicity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastSent;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ClassGroup")
     */
    private $watchedClasses;

    public function __construct()
    {
        $this->watchedClasses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPeriodicity(): ?int
    {
        return $this->periodicity;
    }

    public static function getLabelForPeriodicity(int $periodicity): string
    {
        switch ($periodicity) {
            case self::PERIO_DAILY:
                return 'Tous les jours';
            case self::PERIO_WEEKLY:
                return 'Chaque fin de semaine';
            case self::PERIO_MONTHLY:
                return 'Chaque fin de mois';
            default:
                return '';
        }
    }

    public function getPeriodicityLabel(): string
    {
        return self::getLabelForPeriodicity($this->periodicity);
    }

    public function setPeriodicity(int $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    public function getLastSent(): ?\DateTimeInterface
    {
        return $this->lastSent;
    }

    public function setLastSent(?\DateTimeInterface $lastSent): self
    {
        $this->lastSent = $lastSent;

        return $this;
    }

    /**
     * @return Collection|ClassGroup[]
     */
    public function getWatchedClasses(): Collection
    {
        return $this->watchedClasses;
    }

    public function addWatchedClass(ClassGroup $watchedClass): self
    {
        if (!$this->watchedClasses->contains($watchedClass)) {
            $this->watchedClasses[] = $watchedClass;
        }

        return $this;
    }

    public function removeWatchedClass(ClassGroup $watchedClass): self
    {
        if ($this->watchedClasses->contains($watchedClass)) {
            $this->watchedClasses->removeElement($watchedClass);
        }

        return $this;
    }
}
