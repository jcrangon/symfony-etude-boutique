<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use App\Service\PanierManager;
use Symfony\Component\HttpFoundation\RequestStack;


class ProductController extends AbstractController
{

    private $app;
    private $db;
    private $userInfo;
    private $cartCount;
    private $session;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app, PanierManager $cartManager, RequestStack $requestStack)
    {
        $this->app = $app;
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

    public function index(?string $cat, ?int $id): Response
    {
        if (!isset($cat) || !isset($id)) {
            throw $this->createNotFoundException('');
        }

        // récupération des catégories dans la BDD
        $categories = $this->db->getRepository(Categorie::class)->findAll();

        $product = $this->db->getRepository(Produit::class)->find($id);

        // dump($product);
        // exit();

        return $this->render('product/index.html.twig', [
            'bodyId' => $this->app->getBodyId('PRODUCT_PAGE'),
            'userInfo' => $this->userInfo,
            'categories' => $categories,
            'product' => $product,
            'cat' => $cat,
            'cartCount' => $this->cartCount,
        ]);
    }
}
