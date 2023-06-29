<?php

namespace App\Entity;

use App\Repository\PacientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PacientRepository::class)]
class Pacient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 255)]
    private ?string $healthMatter = null;

    #[ORM\OneToMany(mappedBy: 'appointments', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $patients;

    public function __construct()
    {
        $this->patients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getHealthMatter(): ?string
    {
        return $this->healthMatter;
    }

    public function setHealthMatter(string $healthMatter): static
    {
        $this->healthMatter = $healthMatter;

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Appointment $patient): static
    {
        if (!$this->patients->contains($patient)) {
            $this->patients->add($patient);
            $patient->setAppointments($this);
        }

        return $this;
    }

    public function removePatient(Appointment $patient): static
    {
        if ($this->patients->removeElement($patient)) {
            // set the owning side to null (unless already changed)
            if ($patient->getAppointments() === $this) {
                $patient->setAppointments(null);
            }
        }

        return $this;
    }
}