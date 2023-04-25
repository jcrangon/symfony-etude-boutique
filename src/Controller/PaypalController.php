<?php

namespace App\Controller;

use App\Service\PayPal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AppHelpers;
use App\Service\PanierManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class PaypalController extends AbstractController
{
    private string $bodyId;
    private $app;
    private $db;
    private $userInfo;
    private $cartCount;
    private $session;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app, RequestStack $requestStack, PanierManager $cartManager,)
    {
        $this->app = $app;
        $this->bodyId = $app->getBodyId('ORDER_CONFIRMATION');
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();
        $this->session = $requestStack->getSession();
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

    public function index(PayPal $payPal): Response
    {
        if (!isset($this->session->get('checkoutData')['orderTotals']['totalTTC'])) {
            $this->redirectToRoute('app_category');
        }

        return $this->render('paypal/index.html.twig', [
            'bodyId' => $this->bodyId,
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
            'parameters' => $payPal->getQueryParametersForJsSdk(),
            'paymentMethod' => 'payPal Charge',
            'amount' => (float)$this->session->get('checkoutData')['orderTotals']['totalTTC'],
            'shipping_preference' => $this->session->get('checkoutData')['shippingMethod']['nom'],
        ]);
    }

    public function confirmation(Request $request, PayPal $payPal): Response
    {
        // Some logic to verify if the captured payment is valid
        // ... 

        return $this->render('paypal/order_confirmation.html.twig', [
            'bodyId' => $this->bodyId,
            'cartCount' => $this->cartCount,
            'userInfo' => $this->userInfo,
        ]);
    }
}
