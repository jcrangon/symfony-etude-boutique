<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private $code_activation;

    #[ORM\Column]
    private $reduction;

    #[ORM\ManyToMany(targetEntity: Membre::class, mappedBy: 'coupon')]
    private Collection $membres;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeActivation(): ?string
    {
        return $this->code_activation;
    }

    public function setCodeActivation($code_activation): self
    {
        $this->code_activation = $code_activation;

        return $this;
    }

    public function getReduction(): ?int
    {
        return $this->reduction;
    }

    public function setReduction($reduction): self
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * @return Collection<int, Membre>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->addCoupon($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            $membre->removeCoupon($this);
        }

        return $this;
    }
}
