<?php

namespace App\Entity;


use App\Repository\MembreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: MembreRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]

class Membre implements UserInterface, PasswordAuthenticatedUserInterface

{

    #[ORM\Id]

    #[ORM\GeneratedValue]

    #[ORM\Column]

    private int $id;


    #[ORM\Column(type: 'string', length: 180, unique: true)]

    private $email;


    #[ORM\Column(type: Types::JSON)]

    private $roles;


    /**

     * @var string The hashed password

     */

    #[ORM\Column(type: 'string', nullable: false, options: ["collation" => "utf8mb4_general_ci"])]

    private $password;


    #[ORM\Column(type: 'string', length: 20, nullable: false, options: ["collation" => "utf8mb4_general_ci"])]

    private $pseudo;


    #[ORM\Column(type: 'string', length: 20, nullable: false, options: ["collation" => "utf8mb4_general_ci"])]

    private $nom;


    #[ORM\Column(type: 'string', length: 20, nullable: false, options: ["collation" => "utf8mb4_general_ci"])]

    private $prenom;


    #[ORM\Column(length: 1, columnDefinition: "ENUM('m', 'f') NOT NULL DEFAULT 'm'", options: ["collation" => "utf8mb4"])]

    private $civilite;


    #[ORM\Column(type: 'string', length: 50, nullable: false, options: ["collation" => "utf8mb4_general_ci"])]

    private $ville;


    #[ORM\Column(type: 'integer')]

    private $code_postal;


    #[ORM\Column(type: 'string', length: 50, nullable: false, options: ["collation" => "utf8mb4_general_ci"])]

    private $adresse;


    #[ORM\Column(type: 'integer')]

    private $statut;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]

    private $date_enregistrement;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToOne(targetEntity: Panier::class, mappedBy: 'membre')]
    private ?Panier $panier = null;

    #[ORM\ManyToMany(targetEntity: Coupon::class, inversedBy: 'membres')]
    private Collection $coupon;

    public function __construct()
    {
        $this->coupon = new ArrayCollection();
    }


    public function getId(): ?int

    {

        return $this->id;
    }


    public function getEmail(): ?string

    {

        return $this->email;
    }


    public function setEmail($email): self

    {

        $this->email = $email;


        return $this;
    }


    /**

     * A visual identifier that represents this user.

     *

     * @see UserInterface

     */

    public function getUserIdentifier(): string

    {

        return (string) $this->email;
    }


    /**

     * @see UserInterface

     */

    public function getRoles(): array

    {

        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER

        $roles[] = 'ROLE_USER';


        return array_unique($roles);
    }


    public function setRoles($roles): self

    {

        $this->roles = $roles;


        return $this;
    }


    /**

     * @see PasswordAuthenticatedUserInterface

     */

    public function getPassword(): string

    {

        return $this->password;
    }


    public function setPassword($password): self

    {

        $this->password = $password;


        return $this;
    }


    /**

     * @see UserInterface

     */

    public function eraseCredentials()

    {

        // If you store any temporary, sensitive data on the user, clear it here

        // $this->plainPassword = null;

    }


    public function getPseudo(): ?string

    {

        return $this->pseudo;
    }


    public function setPseudo($pseudo): self

    {

        $this->pseudo = $pseudo;


        return $this;
    }


    public function getNom(): ?string

    {

        return $this->nom;
    }


    public function setNom($nom): self

    {

        $this->nom = $nom;


        return $this;
    }


    public function getPrenom(): ?string

    {

        return $this->prenom;
    }


    public function setPrenom($prenom): self

    {

        $this->prenom = $prenom;


        return $this;
    }


    public function getCivilite(): ?string

    {

        return $this->civilite;
    }


    public function setCivilite($civilite): self

    {

        $this->civilite = $civilite;


        return $this;
    }


    public function getVille(): ?string

    {

        return $this->ville;
    }


    public function setVille($ville): self

    {

        $this->ville = $ville;


        return $this;
    }


    public function getCodePostal(): ?int

    {

        return $this->code_postal;
    }


    public function setCodePostal($code_postal): self

    {

        $this->code_postal = $code_postal;


        return $this;
    }


    public function getAdresse(): ?string

    {

        return $this->adresse;
    }


    public function setAdresse($adresse): self

    {

        $this->adresse = $adresse;


        return $this;
    }


    public function getStatut(): ?int

    {

        return $this->statut;
    }


    public function setStatut($statut): self

    {

        $this->statut = $statut;


        return $this;
    }


    public function getDateEnregistrement(): ?\DateTimeInterface

    {

        return $this->date_enregistrement;
    }


    public function setDateEnregistrement($date_enregistrement): self

    {

        $this->date_enregistrement = $date_enregistrement;


        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Coupon>
     */
    public function getCoupon(): Collection
    {
        return $this->coupon;
    }

    public function addCoupon(Coupon $coupon): self
    {
        if (!$this->coupon->contains($coupon)) {
            $this->coupon->add($coupon);
        }

        return $this;
    }

    public function removeCoupon(Coupon $coupon): self
    {
        $this->coupon->removeElement($coupon);

        return $this;
    }

    public function setPanier(Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getPanier(): ?Panier
    {

        return $this->panier;
    }
}
