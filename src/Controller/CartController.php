<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Service\AppHelpers;
use App\Service\PanierManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{

  private $app;
  private string $bodyId;
  private $db;
  private $userInfo;
  private $cartManager;
  private $cartCount;
  private $session;

  public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app, PanierManager $cartManager, RequestStack $requestStack)
  {
    $this->app = $app;
    $this->bodyId = $app->getBodyId('CART_PAGE');
    $this->db = $doctrine->getManager();
    $this->userInfo = $app->getUser();
    $this->cartManager = $cartManager;

    $this->session = $requestStack->getSession();
    if (null !== $this->userInfo->user) {
      if (null !== $this->session->get('cartCount')) {
        $this->cartCount = (int)$this->session->get('cartCount');
      } else {
        $this->session->set('cartCount', $cartManager->getCartCount($this->userInfo->user));
        $this->cartCount = (int)$this->session->get('cartCount');
      }
    }
  }

  // ajout ajax d'un article dans le panier
  public function index(Request $request): JsonResponse
  {
    $productId = $request->get("productId");
    $product = $this->db->getRepository(Produit::class)->find($productId);

    if ($product) {
      if ($this->cartManager->addToCart($this->userInfo->user, $product)) {
        return $this->json([
          'success' => true,
        ]);
      }
    }

    return $this->json([
      'success' => false,
    ]);
  }

  // affichage de la page du panier
  public function cartDetails(PanierManager $cartManager)
  {
    // on récupère les articles du panier
    $articles = $this->userInfo->user->getPanier()->getArticle();

    // on defini le taux de tva
    $tauxTva = 20;

    // on calcule le total panier
    $totalPanier = $cartManager->calculePanier($this->userInfo->user->getPanier(), $tauxTva);

    // on récupère l'etat d'avancement dans le
    // tunel de commande
    if (null !== $this->session->get('step')) {
      $step = $this->session->get('step');
    } else {
      $step = 1;
      $this->session->set('step', 1);
    }

    // render
    return $this->render('cart/index.html.twig', [
      'bodyId' => $this->bodyId,
      'userInfo' => $this->userInfo,
      'cartCount' => $this->cartCount,
      'articles' => $articles,
      'tableHeadings' => ['Article', 'Désignation', 'Couleur', 'Taille', 'Qté', 'Prix (€)'],
      'totalPanier' => $totalPanier,
      'step' => $step,
    ]);
  }
}
