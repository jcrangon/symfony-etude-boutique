<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Categorie;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use App\Service\PanierManager;
use Symfony\Component\HttpFoundation\RequestStack;

class HomeController extends AbstractController
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
        // on vérifie que la Bdd est prête.
        $this->app->installBdd();

        // récupération des images de carousel dans la BDD
        $carouselImages = $this->db->getRepository(Carousel::class)->findBy(["emplacement" => "home"], ["position" => "ASC"]);

        // marquage de la première image
        // qui devra être notée comme 'active'
        if (isset($carouselImages[0])) {
            $carouselImages[0]->etat = 'active';
        }

        // récupération des catégories dans la BDD
        $categories = $this->db->getRepository(Categorie::class)->findAll();

        // render
        return $this->render('home/index.html.twig', [
            'bodyId' => $this->bodyId,
            'carouselImg' => $carouselImages,
            'carouselId' => 'homeCarousel',
            'userInfo' => $this->userInfo,
            'categories' => $categories,
            'cartCount' => $this->cartCount,

        ]);
    }
}
