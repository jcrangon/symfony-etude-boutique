<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    public const STATUS_FRAUD_SUSPECTED = 'fraud_suspected';
    public const STATUS_PAID = 'paid';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 100)]
    private ?string $paymentMethod = null;

    #[ORM\Column]
    private ?float $amountHT = null;

    #[ORM\Column]
    private ?float $shippingHT = null;

    #[ORM\Column]
    private ?float $totalAmountHT = null;

    #[ORM\Column]
    private ?float $amountTVA = null;

    #[ORM\Column]
    private ?float $totalAmountTTC = null;

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
}
