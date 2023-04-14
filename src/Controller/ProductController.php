<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class ProductController extends AbstractController
{

    private $app;
    private $db;
    private $userInfo;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app)
    {
        $this->app = $app;
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();
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
        ]);
    }
}
