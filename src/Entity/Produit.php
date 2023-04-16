<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 20, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $reference;

    #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $titre;

    #[ORM\Column(type: Types::TEXT, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $description;

    #[ORM\Column(type: 'string', length: 20, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $couleur;

    #[ORM\Column(type: 'string', length: 5, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $taille;

    #[ORM\Column(columnDefinition: "enum('m', 'f') NOT NULL DEFAULT 'm' COLLATE 'utf8mb4_general_ci'")]
    private $sexe;

    #[ORM\Column(type: 'string', length: 250, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $photo;

    #[ORM\Column(type: 'float', nullable: false)]
    private $prix;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $stock;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    private $categorie;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: PanierDetail::class, orphanRemoval: true)]
    private Collection $panierDetails;

    public function __construct()
    {
        $this->panierDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference($reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre($titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur($couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille($taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe($sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix($prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock($stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, PanierDetail>
     */
    public function getPanierDetails(): Collection
    {
        return $this->panierDetails;
    }

    public function addPanierDetail(PanierDetail $panierDetail): self
    {
        if (!$this->panierDetails->contains($panierDetail)) {
            $this->panierDetails->add($panierDetail);
            $panierDetail->setProduit($this);
        }

        return $this;
    }

    public function removePanierDetail(PanierDetail $panierDetail): self
    {
        if ($this->panierDetails->removeElement($panierDetail)) {
            // set the owning side to null (unless already changed)
            if ($panierDetail->getProduit() === $this) {
                $panierDetail->setProduit(null);
            }
        }

        return $this;
    }
}
