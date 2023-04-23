<?php

namespace App\Entity;

use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
class PaymentMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $id;

    #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $img;

    #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $width;

    #[ORM\Column(type: 'string', length: 3, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
