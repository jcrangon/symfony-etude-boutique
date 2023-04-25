<?php

namespace App\Controller;

use App\Entity\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Stripe;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use App\Service\PanierManager;
use Symfony\Component\HttpFoundation\RequestStack;

class StripeController extends AbstractController
{
    private string $bodyId;
    private $app;
    private $db;
    private $userInfo;
    private $cartCount;
    private $session;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app, PanierManager $cartManager, RequestStack $requestStack)
    {
        $this->app = $app;
        $this->bodyId = $app->getBodyId('ORDER_CONFIRMATION');
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();

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
    public function index(): Response
    {
        $method = $this->db->getRepository(PaymentMethod::class)->find((int)$this->session->get('checkoutData')['payment']['method']);
        return $this->render('stripe/index.html.twig', [
            'clef_stripe' => $_ENV["STRIPE_KEY"],
            'bodyId' => $this->bodyId,
            'cartCount' => $this->cartCount,
            'userInfo' => $this->userInfo,
            'orderTotal' => (float)$this->session->get('checkoutData')['orderTotals']['totalTTC'],
            'checkoutData' => $this->session->get('checkoutData'),
            'method' => $method,
        ]);
    }

    public function createCharge(Request $request)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        try {
            Stripe\Charge::create([
                "amount" => (float)$this->session->get('checkoutData')['orderTotals']['totalTTC'] * 100,
                "currency" => "eur",
                "source" => $request->request->get('stripeToken'),
                "description" => "Payment Test"
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_stripe_fail', [
                'error' => $e->getMessage(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_stripe_success', [], Response::HTTP_SEE_OTHER);
    }

    public function orderConfirmation(): Response
    {
        return $this->render('stripe/order_confirmation.html.twig', [
            'bodyId' => $this->bodyId,
            'cartCount' => $this->cartCount,
            'userInfo' => $this->userInfo,
            'orderTotal' => $this->session->get('orderTotal'),
            'paymentMethod' => 'Stripe Charge',
        ]);
    }

    public function paymentFailure(Request $request): Response
    {
        $error = $request->get('error');
        return $this->render('stripe/payment_failure.html.twig', [
            'bodyId' => $this->bodyId,
            'cartCount' => $this->cartCount,
            'userInfo' => $this->userInfo,
            'orderTotal' => $this->session->get('orderTotal'),
            'error' => $error,
        ]);
    }
}
