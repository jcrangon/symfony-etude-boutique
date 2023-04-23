<?php

namespace App\Entity;

use App\Repository\TransporteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransporteurRepository::class)]
class Transporteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $id;

    #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $nom;

    #[ORM\Column(type: 'float', nullable: false)]
    private $prix;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['collation' => 'utf8mb4_general_ci'])]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
