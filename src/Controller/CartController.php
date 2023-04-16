<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Service\AppHelpers;
use App\Service\PanierManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{

  private $app;
  private $db;
  private $userInfo;
  private $cartManager;

  public function __construct(ManagerRegistry $doctrine,  AppHelpers $app, PanierManager $cartManager)
  {
    $this->app = $app;
    $this->db = $doctrine->getManager();
    $this->userInfo = $app->getUser();
    $this->cartManager = $cartManager;
  }

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
}
