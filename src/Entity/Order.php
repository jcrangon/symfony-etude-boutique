<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\IsFalse;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    public const STATUS_FRAUD_SUSPECTED = 'Faude suspectée';
    public const STATUS_PAID = 'Paiement effectué';
    public const PENDING = 'En attente de Paiement';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $id = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $status = self::PENDING;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'integer', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $paymentMethod;

    #[ORM\Column(type: 'float', nullable: false)]
    private $amountHT;

    #[ORM\Column(type: 'float', nullable: false)]
    private $shippingHT;

    #[ORM\Column(type: 'float', nullable: false)]
    private $totalAmountHT;

    #[ORM\Column(type: 'float', nullable: false)]
    private $amountTVA;

    #[ORM\Column(type: 'float', nullable: false)]
    private $totalAmountTTC;

    #[ORM\Column(type: 'string', length: 150, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $shippingMethod;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Membre $membre = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: OrderDetail::class)]
    private Collection $OrderLines;

    public function __construct()
    {
        $this->OrderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getAmountHT(): ?float
    {
        return $this->amountHT;
    }

    public function setAmountHT(float $amountHT): self
    {
        $this->amountHT = $amountHT;

        return $this;
    }

    public function getShippingHT(): ?float
    {
        return $this->shippingHT;
    }

    public function setShippingHT(float $shippingHT): self
    {
        $this->shippingHT = $shippingHT;

        return $this;
    }

    public function getTotalAmountHT(): ?float
    {
        return $this->totalAmountHT;
    }

    public function setTotalAmountHT(float $totalAmountHT): self
    {
        $this->totalAmountHT = $totalAmountHT;

        return $this;
    }

    public function getAmountTVA(): ?float
    {
        return $this->amountTVA;
    }

    public function setAmountTVA(float $amountTVA): self
    {
        $this->amountTVA = $amountTVA;

        return $this;
    }

    public function getTotalAmountTTC(): ?float
    {
        return $this->totalAmountTTC;
    }

    public function setTotalAmountTTC(float $totalAmountTTC): self
    {
        $this->totalAmountTTC = $totalAmountTTC;

        return $this;
    }

    public function getShippingMethod(): ?string
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(string $shippingMethod): self
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): self
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderLines(): Collection
    {
        return $this->OrderLines;
    }

    public function addOrderLine(OrderDetail $orderLine): self
    {
        if (!$this->OrderLines->contains($orderLine)) {
            $this->OrderLines->add($orderLine);
            $orderLine->setOwner($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderDetail $orderLine): self
    {
        if ($this->OrderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getOwner() === $this) {
                $orderLine->setOwner(null);
            }
        }

        return $this;
    }
}
