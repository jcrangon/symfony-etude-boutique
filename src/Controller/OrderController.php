<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use App\Service\PanierManager;
use Symfony\Component\HttpFoundation\RequestStack;

class OrderController extends AbstractController
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
        $this->bodyId = $app->getBodyId('HOME_PAGE');
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
        return $this->render('order/index.html.twig', [
            'bodyId' => $this->bodyId,
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
        ]);
    }
}
