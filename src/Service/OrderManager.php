<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderDetail;
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
  private $userInfo;

  public function __construct(ContainerBagInterface $params, ManagerRegistry $doctrine, Security $security, RequestStack $requestStack, PanierManager $cartManager, AppHelpers $app)
  {
    $this->params = $params;
    $this->doctrine = $doctrine;
    $this->db = $doctrine->getManager();
    $this->security = $security;
    $this->session = $requestStack->getSession();
    $this->cartManager = $cartManager;
    $this->userInfo = $app->getUser();
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

  public function cleanUpAfterCheckout()
  {
    // on cree les lignes de commmande a partire de celles
    // du panier et on les charge dans la commande
    $panier = $this->userInfo->user->getPanier();
    $articles = $panier->getArticle();
    $order = new Order();
    $order->setMembre($this->userInfo->user);
    $order->setCreatedAt(new \Datetime('today'));
    $order->setUpdatedAt(new \Datetime('today'));

    $paymentMethod = $this->session->get('checkoutData')['payment']['method'];
    $amountHT = $this->session->get('checkoutData')['orderTotals']['cartTotals']->totalHT;
    $shippingHT = $this->session->get('checkoutData')['orderTotals']['totalLivraisonHT'];
    $totalAmount = $amountHT + $shippingHT;
    $amountTVA = $this->session->get('checkoutData')['orderTotals']['tva20'];
    $amountTTC = $totalAmount + $amountTVA;
    $shippingMethod = $this->session->get('checkoutData')['shippingMethod']['nom'];

    $order->setPaymentMethod($paymentMethod);
    $order->setAmountHT($amountHT);
    $order->setShippingHT($shippingHT);
    $order->setTotalAmountHT($totalAmount);
    $order->setAmountTVA($amountTVA);
    $order->setTotalAmountTTC($amountTTC);
    $order->setShippingMethod($shippingMethod);
    $order->setStatus(Order::STATUS_PAID);

    foreach ($articles as $article) {
      $ligne = new OrderDetail();
      $ligne->setProduitId($article->getProduit()->getId());
      $ligne->setQuantity($article->getQuantity());
      $ligne->setPrixUnitaire($this->session->get('lignesPanier')[$article->getId()]);
      $this->db->persist($ligne);
      $order->addOrderLine($ligne);
      $panier->removeArticle($article);
    }
    // on vide le panier

    $this->db->persist($panier);
    $this->db->persist($order);
    $this->db->flush();



    $this->session->remove('step');
    $this->session->remove('checkoutData');
    $this->session->remove('lignesPanier');
  }
}
