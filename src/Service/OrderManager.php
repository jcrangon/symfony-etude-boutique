<?php

namespace App\Service;

use App\Entity\Panier;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Component\HttpFoundation\RequestStack;


class OrderManager
{
  private $params;
  private $doctrine;
  private $security;
  private $db;
  private $session;
  private $cartManager;

  public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, PanierManager $cartManager)
  {
    $this->params = $params;
    $this->doctrine = $doctrine;
    $this->db = $doctrine->getManager();
    $this->security = $security;
    $this->session = $requestStack->getSession();
    $this->cartManager = $cartManager;
  }

  public function getCartTotals(Panier $panier, float $tva): stdClass
  {
    return $this->cartManager->calculePanier($panier, $tva);
  }

  public function getTotalLivraison()
  {
    // dd($this->session->get('checkoutData'));
    // exit();

    if (!isset($this->session->get('checkoutData')['shippingMethod']['prix'])) {
      return 0;
    } else {
      $shipping = $this->session->get('checkoutData')['shippingMethod']['prix'];
      return $shipping;
    }
  }
}
