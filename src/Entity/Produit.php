<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

   /*  
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   symfony console doctrine:fixtures:load 
   use Symfony\Component\Validator\Constraints as Assert;
   */

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        min: 5, 
        max: 255, 
        minMessage: 'Le nom doit faire {{ limit }} caractères minimum, vous avez tapé {{ value }}',
        maxMessage: 'Le nom doit faire {{ limit }} caractères maximum'
        )]
    private $nom;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $prix;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $stock;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\Column(type: 'date')]
    private $dateCreation;

    #[ORM\Column(type: 'date', nullable: true)]
    private $dateModification;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    private $categorie;

    public function __construct(){
        $this->setDateCreation(new \DateTime);
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

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
}
