<?php

namespace App\Entity;

use App\Repository\OrderDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDetailRepository::class)]
class OrderDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $produit_id;

    #[ORM\Column(type: 'float', nullable: false)]
    private $prix_unitaire;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $quantity;

    #[ORM\ManyToOne(inversedBy: 'OrderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduitId(): ?int
    {
        return $this->produit_id;
    }

    public function setProduitId(int $produit_id): self
    {
        $this->produit_id = $produit_id;

        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->prix_unitaire;
    }

    public function setPrixUnitaire(float $prix_unitaire): self
    {
        $this->prix_unitaire = $prix_unitaire;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOwner(): ?Order
    {
        return $this->owner;
    }

    public function setOwner(?Order $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
