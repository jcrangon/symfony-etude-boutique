<?php

namespace App\Service;

use App\Entity\Membre;
use App\Entity\Panier;
use App\Entity\PanierDetail;
use App\Entity\Produit;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierManager
{
  private $params;
  private $doctrine;
  private $security;
  private $db;
  private $session;

  public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack)
  {
    $this->params = $params;
    $this->doctrine = $doctrine;
    $this->db = $doctrine->getManager();
    $this->security = $security;
    $this->session = $requestStack->getSession();
  }

  public function addToCart(?Membre $user, ?Produit $product): bool
  {

    if (!$product || !$user) {
      return false;
    }

    // on essaie de récuperer le panier de l'urtilisateur
    // s'il en a un.
    $panier = $user->getPanier();

    if ($panier) {
      // si l'utilisateur a déjà un panier
      $panier->setUpdatedAt(new \datetime('now'));
      // on récupère la collection d'articles dans le panier
      $panierDetails = $panier->getArticle();

      if (!count($panierDetails)) {
        $panierDetail = new PanierDetail();
        $panierDetail->setPanier($panier);
        $panierDetail->setProduit($product);
        $panierDetail->setQuantity(1);
      } else {
        // on boucle pour trouver l'article dans le detail
        // du panier
        foreach ($panierDetails as $article) {
          if ($article->getProduit()->getId() === (int)$product->getId()) {
            // si trouvé on incrémente la quantité
            $article->setQuantity($article->getQuantity() + 1);
            $this->db->persist($panier);
            $this->db->persist($article);
            $this->db->flush();
            $this->session->set('cartCount', (int) $panier->getCountArticle());
            return true;
          }
        }
        // sinon on cree une nouvelle ligne d'enregistrement
        // dans le detail du panier
        $panierDetail = new PanierDetail();
        $panierDetail->setPanier($panier);
        $panierDetail->setProduit($product);
        $panierDetail->setQuantity(1);
        $this->session->set('cartCount', 1);
      }
    } else {
      // si l'utilisateur n'a pas encore de panier
      // on le créé
      $panier = new Panier();
      // on créé une nouvelle ligne d'enregistrement 
      $panierDetail = new PanierDetail();
      // on affecte 
      $panier->setMembre($user);
      $panier->setCreatedAt(new \datetime('now'));
      $panier->setUpdatedAt(new \datetime('now'));
      $panierDetail->setPanier($panier);
      $panierDetail->setProduit($product);
      $panierDetail->setQuantity(1);
      $this->session->set('cartCount', 1);
    }

    // on enregistre dans la bdd
    $this->db->persist($panier);
    $this->db->persist($panierDetail);
    $this->db->flush();
    $this->session->set('cartCount', (int) $panier->getCountArticle());
    return true;
  }

  public function getCartCount(Membre $user): int
  {
    // on recupère le panier de l'utilisateur
    $panier = $user->getPanier();

    // on retourne le nombre d'article dans le panier
    if ($panier) {
      return $panier->getCountArticle();
    } else {
      return 0;
    }
  }

  public function calculePanier(Panier $panier, float $tauxTva): stdClass
  {
    // on récupere les lignes d'articles du panier
    $articles = $panier->getArticle();

    // on initialise le total panier
    $totalTTC = 0;
    // on boucle sur les article pour trouver le prix total ttc
    foreach ($articles as $article) {
      $totalTTC += $article->getProduit()->getPrix();
    }
    // on en deduit le total ht
    $totalHT = round($totalTTC / (($tauxTva / 100) + 1), 2, PHP_ROUND_HALF_UP);

    // on en déduit le montant tva
    $totalTVA = $totalTTC - $totalHT;

    // on definit un objet de données
    $out = new stdClass();
    $out->totalTTC = $totalTTC;
    $out->totalHT = $totalHT;
    $out->totalTVA = $totalTVA;
    $out->tauxTva = $tauxTva;

    // on renvoi cet objet
    return $out;
  }
}
