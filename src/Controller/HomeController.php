<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Categorie;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    private string $bodyId;
    private $app;
    private $db;
    private $userInfo;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app)
    {
        $this->app = $app;
        $this->bodyId = $app->getBodyId('HOME_PAGE');
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();
    }

    public function index(): Response
    {
        // récupération des images de carousel dans la BDD
        $carouselImages = $this->db->getRepository(Carousel::class)->findBy(["emplacement" => "home"], ["position" => "ASC"]);

        // marquage de la première image
        // qui devra être notée comme 'active'
        if (isset($carouselImages[0])) {
            $carouselImages[0]->etat = 'active';
        }

        // si pas d'image dans la BDD
        // on installe les images dans la BDD
        if (!count($carouselImages)) {
            $this->app->installCarousel('home');
        }

        // récupération des catégories dans la BDD
        $categories = $this->db->getRepository(Categorie::class)->findAll();

        // si aucune catégories trouvées on les
        // installe dans la base.
        if (!count($categories)) {
            $this->app->intallCategories();
        }

        // render
        return $this->render('home/index.html.twig', [
            'bodyId' => $this->bodyId,
            'carouselImg' => $carouselImages,
            'carouselId' => 'homeCarousel',
            'userInfo' => $this->userInfo,
            'categories' => $categories,

        ]);
    }
}
