<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Carousel;
use App\Entity\Categorie;
use App\Entity\Produit;
use stdClass;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Security;

class AppHelpers
{
  private $params;
  private $doctrine;
  private $security;
  private $db;

  public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security)
  {
    $this->params = $params;
    $this->doctrine = $doctrine;
    $this->db = $doctrine->getManager();
    $this->security = $security;
  }

  public function getUser()
  {
    $user = $this->security->getUser();
    if ($user) {
      $isLoggedIn = true;
    } else {
      $isLoggedIn = false;
    }
    if ($this->security->isGranted('ROLE_ADMIN')) {
      $isAdmin = true;
    } else {
      $isAdmin = false;
    }
    $userObj = new stdClass();
    $userObj->user = $user;
    $userObj->isAdmin = $isAdmin;
    $userObj->isLoggedIn = $isLoggedIn;
    return $userObj;
  }

  public function getBodyId(string $page): string
  {
    return $this->params->get($page);
  }


  public function installBdd()
  {
    // récupération des images de carousel dans la BDD
    $carouselImages = $this->db->getRepository(Carousel::class)->findBy(["emplacement" => "home"], ["position" => "ASC"]);

    // si pas d'image dans la BDD
    // on installe les images dans la BDD
    if (!count($carouselImages)) {
      $this->installCarousel('home');
    }

    // récupération des catégories dans la BDD
    $categories = $this->db->getRepository(Categorie::class)->findAll();
    // si aucune catégories trouvées on les
    // installe dans la base.
    if (!count($categories)) {
      $this->intallCategories();
    }

    // récupération de tous les produits
    $products = $this->db->getRepository(Produit::class)->findBy([], null, 1, null);
    // si aucun produit trouvées on les
    // installe dans la base.
    if (!count($products)) {
      $this->installProducts();
    }
  }

  private function installCarousel($emplacement): void
  {
    $carouselList = $this->getCarouselList();

    foreach ($carouselList as $singleImg) {
      $img = new Carousel();
      $img->setEmplacement($emplacement);
      $img->setPosition($singleImg['position']);
      $img->setImage($singleImg['image']);
      $this->db->persist($img);
    }
    $this->db->flush();
  }

  private function getCarouselList(): array
  {
    return [
      [
        "image" => "2.jpg",
        "position" => 2,
        "emplacement" => "home",
      ],
      [
        "image" => "1.jpg",
        "position" => 1,
        "emplacement" => "home",
      ],
      [
        "image" => "3.jpg",
        "position" => 3,
        "emplacement" => "home",
      ],
    ];
  }

  // installe les catégories dans la BDD
  private function intallCategories()
  {
    $catList = $this->getCategoryList();

    foreach ($catList as $cat) {
      $category = new Categorie();
      $category->setNom($cat['nom']);
      $this->db->persist($category);
    }
    $this->db->flush();
  }

  // retourne tableau des catégories initiales de la BDD
  private function getCategoryList(): array
  {
    return [
      [
        "nom" => "t-shirt"
      ],

      [
        "nom" => "chemise"
      ],

      [
        "nom" => "pull"
      ],
    ];
  }

  private function installProducts()
  {
    $productList = $this->getProductList();
    foreach ($productList as $product) {
      $produit = new Produit();
      $category = $this->db->getRepository(Categorie::class)->findOneBy(['nom' => $product['cat']]);
      $produit->setCategorie($category);
      $produit->setReference($product['reference']);
      $produit->setTitre($product['titre']);
      $produit->setDescription($product['description']);
      $produit->setCouleur($product['couleur']);
      $produit->setTaille($product['taille']);
      $produit->setSexe($product['sexe']);
      $produit->setPhoto($product['photo']);
      $produit->setPrix($product['prix']);
      $produit->setStock($product['stock']);

      $this->db->persist($produit);
    }

    $this->db->flush();
  }

  private function getProductList(): array
  {
    return [
      [
        "reference" => "11-d-23",
        "cat" => "t-shirt",
        "titre" => "Tshirt Col V",
        "description" => "Tee-shirt en coton flammé liseré contrastant",
        "couleur" => "bleu",
        "taille" => "M",
        "sexe" => "m",
        "photo" => "100_bleu.jpg",
        "prix" => 20,
        "stock" => 53,
      ],

      [
        "reference" => "66-f-15",
        "cat" => "t-shirt",
        "titre" => "Tshirt Col V rouge",
        "description" => "c\'est vraiment un super tshirt en soir&eacute;e !",
        "couleur" => "rouge",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "102_rouge.png",
        "prix" => 15,
        "stock" => 230,
      ],

      [
        "reference" => "88-g-77",
        "cat" => "t-shirt",
        "titre" => "Tshirt Col rond vert",
        "description" => "Il vous faut ce tshirt Made In France !!!",
        "couleur" => "vert",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "103_vert.png",
        "prix" => 29,
        "stock" => 63,
      ],

      [
        "reference" => "55-b-38",
        "cat" => "t-shirt",
        "titre" => "Tshirt jaune",
        "description" => "e jaune reviens &agrave; la mode, non? :-)",
        "couleur" => "jaune",
        "taille" => "S",
        "sexe" => "m",
        "photo" => "101_jaune.png",
        "prix" => 20,
        "stock" => 3,
      ],

      [
        "reference" => "31-p-33",
        "cat" => "t-shirt",
        "titre" => "Tshirt noir original",
        "description" => "voici un tshirt noir tr&egrave;s original :p",
        "couleur" => "noir",
        "taille" => "XL",
        "sexe" => "m",
        "photo" => "2332_full_t-shirt.jpg",
        "prix" => 25,
        "stock" => 80,
      ],

      [
        "reference" => "56-a-65",
        "cat" => "chemise",
        "titre" => "Chemise Blanche",
        "description" => "Les chemises c\'est bien mieux que les tshirts",
        "couleur" => "blanc",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "105_chemiseblanchem.jpg",
        "prix" => 49,
        "stock" => 73,
      ],

      [
        "reference" => "63-s-63",
        "cat" => "chemise",
        "titre" => "Chemise Noir",
        "description" => "Comme vous pouvez le voir c\'est une chemise noir...",
        "couleur" => "blanc",
        "taille" => "M",
        "sexe" => "m",
        "photo" => "106_chemisenoirm.jpg",
        "prix" => 59,
        "stock" => 120,
      ],

      [
        "reference" => "77-p-79",
        "cat" => "pull",
        "titre" => "Pull gris",
        "description" => "Pull gris pour l\'hiver",
        "couleur" => "gris",
        "taille" => "XL",
        "sexe" => "f",
        "photo" => "104_pullgrism2.jpg",
        "prix" => 79,
        "stock" => 99,
      ],

      [
        "reference" => "15-kjf-85",
        "cat" => "t-shirt",
        "titre" => "T-shirt homme à personnaliser",
        "description" => "Superbe tshirt noir personnalisable!",
        "couleur" => "noir",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "1648068059_tshirt_noir_l.webp",
        "prix" => 16,
        "stock" => 15,
      ],
    ];
  }
}
